<?php

namespace App\Repository;

use App\Entity\Matches;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Matches>
 */
class MatchesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Matches::class);
    }

    public function getAllMatches(): array
    {
        $matches = $this->findAll();
        $result = [];

        foreach ($matches as $match) {
            $result[] = [
                'id' => $match->getId(),
                'source_id' => $match->getSourceId(),
                'discipline' => $match->getDiscipline(),
                'match_format' => $match->getMatchFormat(),
                'score1' => $match->getScore1(),
                'score2' => $match->getScore2(),
                'status' => $match->getStatus()?->value,
                'submatches_number' => $match->getSubmatchesNumber(),
                'tournament' => $match->getTournament() ? [
                    'id' => $match->getTournament()->getId(),
                    'name' => $match->getTournament()->getName(),
                ] : null,
                'team1' => $match->getTeam1() ? [
                    'id' => $match->getTeam1()->getId(),
                    'name' => $match->getTeam1()->getName(),
                ] : null,
                'team2' => $match->getTeam2() ? [
                    'id' => $match->getTeam2()->getId(),
                    'name' => $match->getTeam2()->getName(),
                ] : null,
                'subMatches' => array_map(function($sub) {
                    return [
                        'id' => $sub->getId(),
                        'score1' => $sub->getScore1(),
                        'score2' => $sub->getScore2(),
                    ];
                }, $match->getSubMatches()->toArray()),
                'match_date' => $match->getMatchDate()?->format('c'), // ISO 8601
            ];
        }

        return $result;
    }

//    /**
//     * @return Matches[] Returns an array of Matches objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Matches
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
