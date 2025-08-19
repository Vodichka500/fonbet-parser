<?php
namespace App\Enum;

enum MatchStatus: string
{
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
    case UNDEFINED = 'undefined';

    public static function fromEventStatus(int $status): self
    {
        return match ($status) {
            4 => self::CANCELED,
            2 => self::COMPLETED,
            default => self::UNDEFINED,
        };
    }
}
