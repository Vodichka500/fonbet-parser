<?php

namespace App\Controller;

use App\Entity\Matches;
use App\Entity\SubMatches;
use App\Entity\Teams;
use App\Entity\Tournaments;
use App\Enum\MatchStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ParserController extends AbstractController
{

    public function addTestMatch(EntityManagerInterface $em): Response
    {
        $team1 = new Teams();
        $team1->setName('Navi');
        $em->persist($team1);

        $team2 = new Teams();
        $team2->setName('Spirit');
        $em->persist($team2);

        $tournament = new Tournaments();
        $tournament->setName('ESL One');
        $em->persist($tournament);

        $match = new Matches();
        $match->setSourceId('12345');
        $match->setDiscipline('CS2');
        $match->setTournament($tournament);
        $match->setMatchFormat('Best of 3');
        $match->setScore1(2);
        $match->setScore2(0);
        $match->setTeam1($team1);
        $match->setTeam2($team2);
        $match->setStatus(MatchStatus::COMPLETED);
        $match->setSubmatchesNumber(2);
        $em->persist($match);

        $sub1 = new SubMatches();
        $sub1->setMatch($match);
        $sub1->setScore1(16);
        $sub1->setScore2(10);
        $sub1->setTitle('Mirage');
        $em->persist($sub1);

        $sub2 = new SubMatches();
        $sub2->setMatch($match);
        $sub2->setScore1(16);
        $sub2->setScore2(8);
        $sub2->setTitle('Inferno');
        $em->persist($sub2);

        $em->flush();

        return new Response('Test match added with ID: ' . $match->getId());
    }
}
