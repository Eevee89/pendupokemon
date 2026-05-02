<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Pokemon>
 */
class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function getRandomPokemon(array $gens = [1, 2, 3, 4, 5, 6, 7, 8, 9]): ?Pokemon
    {
        return $this->createQueryBuilder('p')
            ->where('p.generation IN (:gens)')
            ->setParameter('gens', $gens)
            ->addSelect('RAND() as HIDDEN rand')
            ->orderBy('rand')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
