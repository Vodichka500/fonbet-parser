<?php

namespace App\Entity;

use App\Repository\SubMatchesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubMatchesRepository::class)]
class SubMatches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $source_id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $score1 = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $score2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'subMatches')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Matches $match = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSourceId(): ?string
    {
        return $this->source_id;
    }

    public function setSourceId(string $source_id): static
    {
        $this->source_id = $source_id;

        return $this;
    }

    public function getScore1(): ?int
    {
        return $this->score1;
    }

    public function setScore1(int $score1): static
    {
        $this->score1 = $score1;

        return $this;
    }

    public function getScore2(): ?int
    {
        return $this->score2;
    }

    public function setScore2(int $score2): static
    {
        $this->score2 = $score2;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getMatch(): ?Matches
    {
        return $this->match;
    }

    public function setMatch(?Matches $match): static
    {
        $this->match = $match;

        return $this;
    }
}
