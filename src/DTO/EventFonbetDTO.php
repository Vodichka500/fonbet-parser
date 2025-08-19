<?php
namespace App\DTO;

class EventFonbetDTO
{
    public function __construct(
        public int $id,
        public int $competitionId,
        public string $team1,
        public string $team2,
        public int $status,
        public int $startTime

    ) {}

    public static function fromArray(array $data): ?self
    {
        if (!isset($data['id'], $data['competitionId'], $data['team1'], $data['team2'], $data['status'], $data['startTime'])) {
            return null;
        }

        return new self(
            (int)$data["id"],
            (int)$data["competitionId"],
            (string)$data["team1"],
            (string)$data["team2"],
            (int)$data["status"],
            (int)$data["startTime"],
        );
    }
}
