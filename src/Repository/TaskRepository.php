<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(ManagerRegistry $registry, UserRepository $userRepository)
    {
        parent::__construct($registry, Task::class);
        $this->userRepository = $userRepository;
    }

    public function findTaskWithUserNull(bool $isDone): array
    {
        $tasks = [];

            $user = $this->userRepository->findAnonyme();

        if ($this->findBy(['user' => null])) {
            foreach ($this->findBy(['user' => null]) as $anonTask) {
                $anonTask->setUser($user);
                $this->getEntityManager()->persist($anonTask);
            }
            $this->getEntityManager()->flush();
        }
        foreach ($this->findBy(['user' => $user]) as $newTask) {
            if ($newTask->isDone() === $isDone) {
                $tasks[] = $newTask;
            }
        }
        return $tasks;
    }
}
