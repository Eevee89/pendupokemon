<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ORM\Table(name: 'hanging_game')]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32)]
    private ?string $discordId = null;

    #[ORM\ManyToOne(targetEntity: Pokemon::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Pokemon $pokemon;

    #[ORM\Column()]
    private string $letters = '';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDiscordId(): ?string
    {
        return $this->discordId;
    }

    public function setDiscordId(string $discordId): static
    {
        $this->discordId = $discordId;

        return $this;
    }

    public function getPokemon(): Pokemon
    {
        return $this->pokemon;
    }

    public function setPokemon(Pokemon $pokemon): static
    {
        $this->pokemon = $pokemon;

        return $this;
    }

    public function getLetters(): string
    {
        return $this->letters;
    }

    public function setLetters(string $letters): static
    {
        $this->letters = $letters;

        return $this;
    }

    public function getMaskedWord(): string
    {
        $word = strtoupper($this->pokemon->getName());
        $playedLetters = str_split(strtoupper($this->letters));
        $display = "";

        foreach (str_split($word) as $letter) {
            if (in_array($letter, $playedLetters) || $letter === '-' || $letter === ' ') {
                $display .= $letter . " ";
            } else {
                $display .= "_ ";
            }
        }

        return trim($display);
    }
}
