<?php

namespace App\Service;

use App\DTO\CompetitionFonbetDTO;
use App\DTO\EventFonbetDTO;
use App\DTO\EventMiscsFonbetDTO;
use App\DTO\SportFonbetDTO;
use App\Entity\Matches;
use App\Entity\SubMatches;
use App\Entity\Teams;
use App\Entity\Tournaments;
use App\Enum\MatchStatus;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TournamentListItem
{
    public string $id;
    public string $name;
    public string $discipline_name;
    public string $match_format;

    public function __construct(string $id, string $name, string $discipline_name, string $match_format){
        $this->id = $id;
        $this->name = $name;
        $this->discipline_name = $discipline_name;
        $this->match_format = $match_format;
    }
}

class FonbetParserService
{
    private HttpClientInterface $client;
    private EntityManagerInterface $em;
    private LoggerInterface $logger;

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->em = $em;
        $this->logger = $logger;
    }

    public function parseMatchesFromFonbet(
        int $days = 1,
        ?string $tournamentName = null,
        ?string $teamName = null,
        ?string $status = null,
        ?OutputInterface $output = null
    )
    {
        $this->logger->info('=== Start parsing matches ===');

        // CALCULATE DATES TO FETCH
        $now = new \DateTime();
        $datesToFetch = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $datesToFetch[] = (clone $now)->modify("-$i days")->format('Y-m-d');
        }

        foreach ($datesToFetch as $lineDate) {

            $this->logger->info("=== Processing date: $lineDate ===");

            // DOWNLOAD DATA FROM API
            try {
                $data = $this->fetchDataFromFonbet($lineDate);
                $this->logger->info("Data successfully downloaded from Fonbet");
            } catch (\Throwable $e) {
                $this->logger->error("Error loading data from Fonbet: " . $e->getMessage());
                return;
            }

            // GET ESPORTS CATEGORY ID
            try {
                $esportsId = $this->getEsportsCategotyId($data);
                $this->logger->info("Successfully found Esport category ID for date $lineDate");
            } catch (\Throwable $e) {
                $this->logger->error("Error finding Esport category ID for date {$lineDate}: " . $e->getMessage());
                return;
            }

            // GET FILTERED COMPETITIONS
            try {
                $competitions = $this->filterCompetitions($data, (int)$esportsId, $tournamentName);
                $this->logger->info("Successfully filtered competitions");
            } catch (\Throwable $e) {
                $this->logger->error("Error with filtering competitions: " . $e->getMessage());
                return;
            }

            // GET FILTERED EVENTS
            try {
                $events = $this->filterEvents($data, $teamName, $status, $competitions);
                $this->logger->info("Successfully filtered events");
            } catch (\Throwable $e) {
                $this->logger->error("Error with filtering events: " . $e->getMessage());
                return;
            }

            // GET FILTERED MISCS
            $eventMiscs = $this->filterEventMiscs($data, $events);

            $this->logger->info("=== End processing date: $lineDate ===");

            $this->logger->info("=== Start saving matches from $lineDate to local DB ===");

            foreach ($events as $event){
                // SKIP IF EVENT HAS NO COMPETITION
                $competitionData = $competitions[$event->competitionId] ?? null;
                if (!$competitionData) continue;

                if (!in_array((int) $event->status, [2, 4], true)) {
                    continue;
                }

                // WHEN STATUS IS 2, EVENT MUST HAVE EVENT_MISCS
                if ((int) $event->status === 2 && !isset($eventMiscs[$event->id]) ) {
                    continue;
                }

                $eventMiscsData = $eventMiscs[$event->id] ?? null;

                try {

                    // SAVE OR FIND TOURNAMENT
                    $tournamentDB =$this->em->getRepository(Tournaments::class)->findOneBy(['name' => strtolower($competitionData->name)]);
                    if(!$tournamentDB){
                        $tournamentDB = new Tournaments();
                        $tournamentDB->setName(strtolower($competitionData->name));
                        $this->em->persist($tournamentDB);
                        $this->em->flush();
                    }

                    // SAVE OR FIND TEAM1
                    $team1DB = $this->em->getRepository(Teams::class)->findOneBy(['name' => strtolower($event->team1)]);
                    if(!$team1DB){
                        $team1DB = new Teams();
                        $team1DB->setName(strtolower($event->team1));
                        $this->em->persist($team1DB);
                        $this->em->flush();
                    }

                    // SAVE OR FIND TEAM2
                    $team2DB = $this->em->getRepository(Teams::class)->findOneBy(['name' => strtolower($event->team2)]);
                    if(!$team2DB){
                        $team2DB = new Teams();
                        $team2DB->setName(strtolower($event->team2));
                        $this->em->persist($team2DB);
                        $this->em->flush();
                    }

                    // SAVE OR FIND MATCH
                    $matchDB = $this->em->getRepository(Matches::class)->findOneBy(['source_id' => $event->id]);
                    if(!$matchDB){
                        $matchDB = new Matches();
                        $matchDB->setSourceId($event->id);
                        $matchDB->setDiscipline(strtolower($competitionData->discipline_name));
                        $matchDB->setMatchFormat(strtolower($competitionData->match_format));
                        $matchDB->setScore1($eventMiscsData->score1 ?? null);
                        $matchDB->setScore2($eventMiscsData->score2 ?? null);
                        $matchDB->setTeam1($team1DB);
                        $matchDB->setTeam2($team2DB);
                        $matchDB->setStatus(MatchStatus::fromEventStatus(intval($event->status)));
                        $matchDB->setTournament($tournamentDB);
                        $matchDB->setSubmatchesNumber(
                            $eventMiscsData && !empty($eventMiscsData->subScores)
                                ? count($eventMiscsData->subScores)
                                : 1
                        );
                        $matchDB->setMatchDate(
                            (new \DateTimeImmutable('@' . $event->startTime))->setTimezone(new \DateTimeZone('UTC'))
                        );
                        $this->em->persist($matchDB);
                        $this->em->flush();
                    }

                    // SAVE OR FIND SUBMATCH
                    if (!empty($eventMiscsData->subScores)) {
                        foreach ($eventMiscsData->subScores as $subScoreDTO) {
                            $subMatch = $this->em->getRepository(SubMatches::class)
                                ->findOneBy([
                                    'source_id' => $subScoreDTO->scoreIndex,
                                    'match' => $matchDB->getId(),
                                ]);
                            if(!$subMatch){
                                $subMatch = new SubMatches();
                                $subMatch->setSourceId($subScoreDTO->scoreIndex);
                                $subMatch->setScore1($subScoreDTO->score1);
                                $subMatch->setScore2($subScoreDTO->score2);
                                $subMatch->setTitle($subScoreDTO->title);
                                $subMatch->setMatch($matchDB);

                                $this->em->persist($subMatch);
                            }
                        }
                        $this->em->flush();
                    }

                    $this->logger->info("Successfully processed event {$event->id}");
                } catch (\Throwable $e) {
                    $this->logger->error("Error processing event {$event->id}: " . $e->getMessage());
                    continue;
                }
            }
            $this->logger->info("=== End saving matches to local DB ===");
        }
    }

    /**
     * FETCH AND MERGE DATA FROM ALL Dates IN datesToFetch
     * @param string[] $datesToFetch
     */
    private function fetchDataFromFonbet(string $lineDate)
    {
        $url = "https://clientsapi01.by0e87-resources.by/results/v2/getByDate?lang=en&packetVersion=0&lineDate={$lineDate}&scopeMarket=700";
        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException("Error fetching data for date {$lineDate}");
        }

        $data = $response->toArray();
        if (!isset($data['sports'], $data['events'], $data['eventMiscs'], $data['competitions'])) {
            throw new \RuntimeException('Invalid data format from Fonbet');
        }

        return $data;
    }

    /**
     * RETURN CATEGORY ID OF ESPORTS IN FONFET SYSTEM
     * @param array $allData
     * @return int
     */
    private function getEsportsCategotyId(array $allData)
    {
        $esportsId = null;
        foreach ($allData['sports'] as $sportData) {
            $sport = SportFonbetDTO::fromArray($sportData);
            if (!$sport) {
                continue;
            }

            $name = strtolower($sport->name);
            if ($name === "esports" || $name === "esport") {
                $esportsId = $sport->id;
                break;
            }
        }
        if (!$esportsId) {
            throw new \RuntimeException('ID of category "Esports" not found. Maybe category name was updated by Fonbet');
        }
        return $esportsId;
    }


    /**
     * FILTER COMPETITIONS AND GET ONLY CONNECTED WITH ESPORTS (CS2 and DOTA) AND BY TOURNAMENT NAME IF PROPERTY EXIST)
     *
     * @param array $allData
     * @param int $esportsId
     * @param string|null $tournamentName
     * @return array
     */
    private function filterCompetitions(array $allData, int $esportsId, string | null $tournamentName): array
    {
        $competitions = [];
        foreach ($allData['competitions'] as $competitionData) {
            $competition = CompetitionFonbetDTO::fromArray($competitionData);
            if (!$competition) {
                continue;
            }

            if ((int)$competition->sportId === $esportsId) {
                $name = strtolower($competition->name);
                if($tournamentName && stripos($competition->name, $tournamentName) === false){
                    continue;
                }

                if (preg_match('/bo\d+$/i', $name, $matches)) {
                    $matchFormat = $matches[0]; // bo3, bo2 и т.д.
                } else {
                    $matchFormat = null; // если формат не указан
                }

                if (str_contains($name, "counter-strike")) {
                    $competitions[$competition->id] = new TournamentListItem(
                        $competition->id,
                        $competition->name ,
                        "counter-strike",
                        $matchFormat
                    );
                    continue;
                }

                if (str_contains($name, "dota 2")) {
                    $competitions[$competition->id] = new TournamentListItem(
                        $competition->id,
                        $competition->name ,
                        "dota 2",
                        $matchFormat
                    );
                }
            }
        }
        if (!$competitions) {
            throw new \RuntimeException('No competitions found at this day');
        }
        return $competitions;
    }

    /**
     * FILTER EVENTS AND GET ONLY CONNECTED WITH EXISTING COMPETITIONS AND BY TEAMNAME AND BY STATUS IF PROPERTY EXIST)
     *
     * @param array $allData
     * @param string|null $teamName
     * @param string|null $status
     * @param array $competitions
     * @return array
     */
    private function filterEvents(array $allData, string | null $teamName, string | null $status, array $competitions)
    {
        $events = [];
        foreach ($allData['events'] as $eventData){
            $event = EventFonbetDTO::fromArray($eventData);
            if (!$event || !isset($competitions[$event->competitionId])) {
                continue;
            }
            if($teamName && (stripos($event->team1, $teamName) === false && stripos($event->team2, $teamName) === false)){
                continue;
            }
            if($status && $event->status != $status){
                continue;
            }
            $events[$event->id] = $event;
        }
        if (!$events) {
            throw new \RuntimeException('No events found at this day');
        }
        return $events;
    }


    /**
     * FILTER EVENTS AND GET ONLY CONNECTED WITH EXISTING EVENTS
     *
     * @param array $allData
     * @param array $events
     * @return array
     */
    private function filterEventMiscs(array $allData, array $events)
    {
        $eventMiscs = [];
        foreach ($allData["eventMiscs"] as $eventMiscsData){
            $eventMiscsItem = EventMiscsFonbetDTO::fromArray($eventMiscsData);
            if (!$eventMiscsItem || !isset($events[$eventMiscsItem->id])) {
                continue;
            }
            $eventMiscs[$eventMiscsItem->id] = $eventMiscsItem;
        }
        return $eventMiscs;
    }
}
