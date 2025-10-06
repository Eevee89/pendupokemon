<?php

namespace App\Entity;

use App\Repository\PokemonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $pokedex = 0;

    #[ORM\Column(length: 32)]
    private string $name = "";

    #[ORM\Column(type: Types::SMALLINT)]
    private int $generation = 1;

    #[ORM\Column(length: 32)]
    private string $type1 = "";

    #[ORM\Column(length: 32, nullable: true)]
    private ?string $type2 = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getPokedex(): int
    {
        return $this->pokedex;
    }

    public function setPokedex(int $pokedex): static
    {
        $this->pokedex = $pokedex;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getGeneration(): int
    {
        return $this->generation;
    }

    public function setGeneration(int $generation): static
    {
        $this->generation = $generation;

        return $this;
    }

    public function getType1(): string
    {
        return $this->type1;
    }

    public function setType1(string $type1): static
    {
        $this->type1 = $type1;

        return $this;
    }

    public function getType2(): ?string
    {
        return $this->type2;
    }

    public function setType2(?string $type2): static
    {
        $this->type2 = $type2;

        return $this;
    }
}
