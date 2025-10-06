<?php

namespace App\Service;

use App\Repository\UserRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserManager {

    private UserRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $repository, EntityManagerInterface $entityManager) 
    {
        $this->repository = $repository;
        $this->em = $entityManager;
    }

    public function findByUsername(string $username): ?User
    {
        return $this->repository->findOneBy(["username" => $username]);
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findAll(): array 
    {
        return $this->repository->findAllWithoutPassword();
    }

    public function create(string $username, string $password): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPassword(password_hash($password, PASSWORD_BCRYPT));

        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function updateScore(User $user, int $score) {
        if (null === $user) {
            return false;
        }

        $oldScore = $this->repository->findScoreById($user->getId());
        $oldScore = $oldScore[0]["score"];

        if ($score > $oldScore) {
            $user->setScore($score);
            $this->em->persist($user);
            $this->em->flush();
        }

        return true;
    }
}