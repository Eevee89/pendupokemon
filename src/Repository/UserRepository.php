<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findAllWithoutPassword(): array
    {
        return $this->createQueryBuilder('u')
            ->select("u.username, u.score")
            ->orderBy('u.score', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
    * @return int Returns the score of the user
    */
    public function findScoreById(int $id): ?array
    {
        return $this->createQueryBuilder('u')
            ->select("u.score")
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getScalarResult()
        ;
    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    
}
