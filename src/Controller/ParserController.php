<?php

namespace App\Controller;

use App\Repository\MatchesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParserController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(MatchesRepository $matchesRepository): Response
    {
        $matches = $matchesRepository->getAllMatches();
        $logFile = dirname(__DIR__, 2) . '/var/log/parser.log';
        $logs = [];
        if (file_exists($logFile)) {
            $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }
        return $this->render('home.html.twig', [
            'matches' => $matches,
            'logs' => $logs,
        ]);
    }
}
