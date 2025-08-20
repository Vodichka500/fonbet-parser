<?php

namespace App\Command;

use App\Service\FonbetParserService;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(name: 'app:parse-matches')]
class ParseMatchesCommand extends Command
{
    private FonbetParserService $parserService;
    private LoggerInterface $logger;

    public function __construct(FonbetParserService $parserService, LoggerInterface $logger)
    {
        parent::__construct();
        $this->parserService = $parserService;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Parsing matches from selected source (Fonbet, etc.)')
            ->addOption('source', null, InputOption::VALUE_REQUIRED, 'Data source', 'Fonbet')
            ->addOption('days', null, InputOption::VALUE_REQUIRED, 'Number of days to parse', 1)
            ->addOption('tournament', null, InputOption::VALUE_OPTIONAL, 'Tournament name', null)
            ->addOption('team', null, InputOption::VALUE_OPTIONAL, 'Team name', null)
            ->addOption('status', null, InputOption::VALUE_OPTIONAL, 'Match status (all, completed, canceled)', 'all');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $source = $input->getOption('source');
        $days = (int)$input->getOption('days');
        $tournamentName = $input->getOption('tournament');
        $teamName = $input->getOption('team');

        $statusInput = $input->getOption('status');
        $statusCode = match (strtolower($statusInput)) {
            'completed' => 2,
            'canceled' => 4,
            default => null,
        };

        $this->runParser($days, $tournamentName, $teamName, $statusCode, $output, $source);

        return Command::SUCCESS;
    }

    private function runParser(int $days, ?string $tournamentName, ?string $teamName, ?int $statusCode, OutputInterface $output, string $source): int
    {
        try {
            if ($source === 'Fonbet') {
                $this->parserService->parseMatchesFromFonbet($days, $tournamentName, $teamName, $statusCode, $output);
                $this->logger->info("Parsing completed successfully!");
                return Command::SUCCESS;
            }
            $this->logger->error("Invalid source selected.");
            return Command::INVALID;
        } catch (\Throwable $e) {
            $this->logger->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
