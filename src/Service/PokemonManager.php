<?php

namespace App\Service;

use App\Repository\PokemonRepository;
use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;

class PokemonManager {

    public function __construct(
        private PokemonRepository $repository, 
        private EntityManagerInterface $entityManager
    ) { }

    public function getRandomPokemon(?string $generations = null): ?Pokemon
    {
        if (null === $generations) {
            return $this->repository->getRandomPokemon();
        }

        try {
            $gens = array_map('intval', explode(',', $generations));

            return $this->repository->getRandomPokemon($gens);
        } catch (\Throwable $e) {
            return null;
        }
    }
}