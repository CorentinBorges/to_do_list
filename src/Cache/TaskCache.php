<?php


namespace App\Cache;


use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\Security;

class TaskCache
{
    /**
     * @var FilesystemAdapter
     */
    private $filesystemAdapter;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var TaskRepository
     */
    private $taskRepository;

    public function __construct(
        UserRepository $userRepository,
        Security $security,
        TaskRepository $taskRepository
    )
    {
        $this->filesystemAdapter = new FilesystemAdapter();
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->taskRepository = $taskRepository;
    }

    public function getList(string $itemName, int $expiredAfter, bool $taskDone, User $user=null )
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->filesystemAdapter->getItem($itemName);
//        $this->filesystemAdapter->delete($itemName);
        if (!$element->isHit() || $_SERVER['APP_ENV'] === 'test') {
            $tasks = $this->taskRepository->findBy(['user' => $user, 'isDone' => $taskDone]);
            if ($this->security->isGranted('ROLE_ADMIN')) {
                    if ($this->userRepository->findAnonyme()) {
                        $anonUser = $this->userRepository->findAnonyme();
                        foreach ($this->taskRepository->findBy(['user' => $anonUser]) as $newTask){
                            if ($newTask->isDone() === $taskDone) {
                                $tasks[] = $newTask;
                            }
                        }
                    }
                }
            $element->expiresAfter($expiredAfter);
            $element->set($tasks);
            $this->filesystemAdapter->save($element);
            }
        return $element->get();
    }

    public function deleteCache(string $itemName)
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->filesystemAdapter->getItem($itemName);
        if ($element->isHit()) {
            $this->filesystemAdapter->delete($itemName);
        }
    }
}