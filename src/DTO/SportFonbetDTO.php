<?php
namespace App\DTO;

class SportFonbetDTO
{
    public function __construct(
        public int $id,
        public string $name
    ) {}

    public static function fromArray(array $data): ?self
    {
        if (!isset($data['id'], $data['name'])) {
            return null;
        }

        return new self(
            (int)$data['id'],
            (string)$data['name']
        );
    }
}
