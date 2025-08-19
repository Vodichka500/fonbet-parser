<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use App\Service\FonbetParserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController
{
    private FonbetParserService $parser;

    public function __construct(FonbetParserService $parser)
    {
        $this->parser = $parser;
    }
    #[\Symfony\Component\Routing\Annotation\Route('/test_parser', name: 'test_parser')]
    public function test(): Response
    {
        $data = $this->parser->parseMatches(2); // дата для примера
        return $this->json($data);
    }
}
