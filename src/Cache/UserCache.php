<?php

namespace App\Cache;

use App\Repository\UserRepository;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\Security;

class UserCache
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var FilesystemAdapter
     */
    private $cache;

    public function __construct(
        UserRepository $userRepository,
        Security $security
    ) {

        $this->userRepository = $userRepository;
        $this->security = $security;
        $this->cache = new FilesystemAdapter();
    }

    public function getList(string $itemName, int $expiredAt)
    {
        /**
         * @var CacheItemInterface $element
         */
        $element = $this->cache->getItem($itemName);
//        $this->cache->delete($itemName);
        if (!$element->isHit()) {
            $users = $this->userRepository->findAll();
            $element->expiresAfter($expiredAt);
            $element->set($users);
            $this->cache->save($element);
        }

        return $element->get();
    }

    public function delete(string $itemName)
    {
        $this->cache->delete($itemName);
    }
}
