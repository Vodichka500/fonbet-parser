<?php
namespace App\DTO;

class CompetitionFonbetDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public int $sportId
    ) {}

    public static function fromArray(array $data): ?self
    {
        if (!isset($data['id'], $data['name'], $data['sportId'])) {
            return null;
        }

        return new self(
            (int)$data['id'],
            (string)$data['name'],
            (int)$data['sportId']
        );
    }
}
