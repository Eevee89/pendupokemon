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
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
        SELECT id FROM pokemon 
        WHERE generation IN (:gens) 
        ORDER BY RAND() 
        LIMIT 1
    ';

        $resultSet = $conn->executeQuery($sql, [
            'gens' => $gens
        ], [
            'gens' => \Doctrine\DBAL\ArrayParameterType::INTEGER
        ]);

        $id = $resultSet->fetchOne();

        return $id ? $this->find($id) : null;
    }
}
