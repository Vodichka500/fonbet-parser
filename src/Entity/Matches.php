<?php

namespace App\Entity;

use App\Enum\MatchStatus;
use App\Entity\Tournaments;
use App\Entity\Teams;
use App\Repository\MatchesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MatchesRepository::class)]
class Matches
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $source_id = null;

    #[ORM\Column(length: 255)]
    private ?string $discipline = null;

    #[ORM\ManyToOne(targetEntity: Tournaments::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Tournaments $tournament = null;

    #[ORM\Column(length: 255)]
    private ?string $match_format = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $score1 = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $score2 = null;

    #[ORM\ManyToOne(targetEntity: Teams::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Teams $team1 = null;

    #[ORM\ManyToOne(targetEntity: Teams::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Teams $team2 = null;

    #[ORM\Column(type: 'string', enumType: MatchStatus::class)]
    private ?MatchStatus $status = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $submatches_number = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $match_date = null;

    /**
     * @var Collection<int, SubMatches>
     */
    #[ORM\OneToMany(targetEntity: SubMatches::class, mappedBy: 'match', orphanRemoval: true)]
    private Collection $subMatches;

    public function __construct()
    {
        $this->subMatches = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
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

    public function getDiscipline(): ?string
    {
        return $this->discipline;
    }

    public function setDiscipline(string $discipline): static
    {
        $this->discipline = $discipline;

        return $this;
    }

    public function getTournament(): ?Tournaments
    {
        return $this->tournament;
    }

    public function setTournament(?Tournaments $tournament): static
    {
        $this->tournament = $tournament;
        return $this;
    }


    public function getMatchFormat(): ?string
    {
        return $this->match_format;
    }

    public function setMatchFormat(string $match_format): static
    {
        $this->match_format = $match_format;

        return $this;
    }

    public function getScore1(): ?int
    {
        return $this->score1;
    }

    public function setScore1(?int $score1): static
    {
        $this->score1 = $score1;

        return $this;
    }

    public function getScore2(): ?int
    {
        return $this->score2;
    }

    public function setScore2(?int $score2): static
    {
        $this->score2 = $score2;

        return $this;
    }

    public function getTeam1(): ?Teams
    {
        return $this->team1;
    }

    public function setTeam1(?Teams $team1): static
    {
        $this->team1 = $team1;
        return $this;
    }

    public function getTeam2(): ?Teams
    {
        return $this->team2;
    }

    public function setTeam2(?Teams $team2): static
    {
        $this->team2 = $team2;
        return $this;
    }

    public function getStatus(): ?MatchStatus
    {
        return $this->status;
    }

    public function setStatus(?MatchStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getSubmatchesNumber(): ?int
    {
        return $this->submatches_number;
    }

    public function setSubmatchesNumber(int $submatches_number): static
    {
        $this->submatches_number = $submatches_number;

        return $this;
    }

    /**
     * @return Collection<int, SubMatches>
     */
    public function getSubMatches(): Collection
    {
        return $this->subMatches;
    }

    public function addSubMatch(SubMatches $subMatch): static
    {
        if (!$this->subMatches->contains($subMatch)) {
            $this->subMatches->add($subMatch);
            $subMatch->setMatch($this);
        }

        return $this;
    }

    public function removeSubMatch(SubMatches $subMatch): static
    {
        if ($this->subMatches->removeElement($subMatch)) {
            // set the owning side to null (unless already changed)
            if ($subMatch->getMatch() === $this) {
                $subMatch->setMatch(null);
            }
        }

        return $this;
    }

    public function getMatchDate(): ?\DateTimeInterface
    {
        return $this->match_date;
    }

    public function setMatchDate(?\DateTimeInterface $matchDate): self
    {
        $this->match_date = $matchDate;
        return $this;
    }
}
