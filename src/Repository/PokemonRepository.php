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

    public function getRandomPokemon(?array $gens = null): ?Pokemon
    {
        $ids = $this->createQueryBuilder('p')
            ->select('p.id');
        
        if (null !== $gens && count($gens) > 0) {
            $ids = $ids->where("p.generation IN (:gens)")
                ->setParameter("gens", $gens);
        }
        $ids = $ids->getQuery()
            ->getScalarResult();

        if (0 === $ids) {
            return null;
        }

        $ids = array_column($ids, "id");

        $offset = mt_rand(0, count($ids) - 1);

        return $this->find($ids[$offset]);
    }
}
