<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LogsController extends AbstractController
{
    #[Route('/logs/clear', name: 'clear_logs', methods: ['POST'])]
    public function clearLogs(): JsonResponse
    {
        $logFile = $this->getParameter('kernel.project_dir') . '/var/log/parser.log';

        if (file_exists($logFile)) {
            file_put_contents($logFile, ""); // очистка файла
        }

        return new JsonResponse(['status' => 'ok']);
    }
}
