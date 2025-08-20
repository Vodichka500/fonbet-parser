<?php

namespace App\DTO;

class SubScoreDTO
{
    public function __construct(
        public int $scoreIndex,
        public int $score1,
        public int $score2,
        public string $title
    ){}

    public static function fromArray(array $data): ?self
    {
        if (!isset($data['scoreIndex'], $data['score1'], $data['score2'], $data['title'])) {
            return null;
        }

        return new self(
            (int)$data['scoreIndex'],
            (int)$data['score1'],
            (int)$data['score2'],
            (string)$data['title']
        );
    }
}
