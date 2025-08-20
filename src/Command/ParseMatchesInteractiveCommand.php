<?php

namespace App\Command;

use App\Service\FonbetParserService;
use React\EventLoop\Loop;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(name: 'app:parse-matches-interactive')]
class ParseMatchesInteractiveCommand extends Command
{
    private FonbetParserService $parserService;

    public function __construct(FonbetParserService $parserService)
    {
        parent::__construct();
        $this->parserService = $parserService;
    }

    protected function configure(): void
    {
        $this->setDescription('Parsing matches from selected source (Fonbet, etc.)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        // 0. Source
        $sourceQuestion = new ChoiceQuestion(
            'Select data source (default: Fonbet)',
            ['Fonbet'],
            0
        );
        $sourceQuestion->setErrorMessage("Invalid data source");
        $source = $helper->ask($input, $output, $sourceQuestion);
        $output->writeln("Source: " . $source);

        // 1. Days
        $daysQuestion = new Question('Enter number of days to parse [default 1]: ', 1);
        $days = (int)$helper->ask($input, $output, $daysQuestion);

        // 2. Tournament
        $tournamentQuestion = new Question('Enter tournament name (optional, press Enter to skip): ', null);
        $tournamentName = $helper->ask($input, $output, $tournamentQuestion);

        // 3. Team
        $teamQuestion = new Question('Enter team name (optional, press Enter to skip): ', null);
        $teamName = $helper->ask($input, $output, $teamQuestion);

        // 4. Status
        $statusQuestion = new ChoiceQuestion(
            'Select match status (default: All matches)',
            ['All matches', 'Completed matches', 'Canceled matches'],
            0
        );
        $statusQuestion->setErrorMessage('Invalid status.');
        $status = $helper->ask($input, $output, $statusQuestion);
        $output->writeln("Status: " . $status);

        $statusCode = match ($status) {
            'Completed matches' => 2,
            'Canceled matches' => 4,
            default => null,
        };

        // 5. Optional interval
        $intervalQuestion = new Question('Enter repeat interval in hours (optional, press Enter to skip): ', null);
        $interval = $helper->ask($input, $output, $intervalQuestion);

        if (!$interval) {
            return $this->runParser($days, $tournamentName, $teamName, $statusCode, $output, $source);
        }

        $loop = Loop::get();
        $seconds = (float)$interval * 3600;
        $output->writeln("<comment>Parser will run every {$interval} hours...</comment>");

        // Запуск периодического парсинга
        $loop->addPeriodicTimer($seconds, function () use ($days, $tournamentName, $teamName, $statusCode, $output, $source, $interval) {
            $this->runParser($days, $tournamentName, $teamName, $statusCode, $output, $source);
            $output->writeln("<comment>Next parse in {$interval} hours...</comment>");
        });

        // Первый запуск сразу
        $this->runParser($days, $tournamentName, $teamName, $statusCode, $output, $source);
        $loop->run();

        return Command::SUCCESS;
    }

    private function runParser(int $days, ?string $tournamentName, ?string $teamName, ?int $statusCode, OutputInterface $output, string $source): int
    {
        try {
            if ($source === 'Fonbet') {
                $this->parserService->parseMatchesFromFonbet($days, $tournamentName, $teamName, $statusCode, $output);
                $output->writeln("<info>Parsing completed successfully!</info>");
                return Command::SUCCESS;
            }

            $output->writeln('<error>Invalid source selected.</error>');
            return Command::INVALID;
        } catch (\Throwable $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }
}
