<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController
{
    #[Route('/lucky/number/')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return new Response(
            '<html><body>TEST, RANDOM NUMBER: '.$number.'</body></html>'
        );
    }
}
