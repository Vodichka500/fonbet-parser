<?php

namespace App\DTO;

class EventMiscsFonbetDTO
{
    public function __construct(
        public int $id,
        public int $score1,
        public int $score2,
        public array $subScores,
        public int $winningTeam)
    {}

    public static function fromArray(array $data): ?self
    {
        if (!isset($data['id'], $data['score1'], $data['score2'], $data['winningTeam'])) {
            return null;
        }

        $subScores = [];
        if (!empty($data['subScores']) && is_array($data['subScores'])) {
            foreach ($data['subScores'] as $subScoreData) {
                $subScore = SubScoreDTO::fromArray($subScoreData);
                if ($subScore) {
                    $subScores[] = $subScore;
                }
            }
        }

        return new self(
            id: (int)$data['id'],
            score1: (int)$data['score1'],
            score2: (int)$data['score2'],
            subScores:  $subScores,
            winningTeam:  (int)$data['winningTeam']
        );
    }
}
