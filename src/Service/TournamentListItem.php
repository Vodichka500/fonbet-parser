<?php

namespace App\Service;

class TournamentListItem
{
    public string $id;
    public string $name;
    public string $discipline_name;
    public string $match_format;

    public function __construct(string $id, string $name, string $discipline_name, string $match_format)
    {
        $this->id = $id;
        $this->name = $name;
        $this->discipline_name = $discipline_name;
        $this->match_format = $match_format;
    }
}
