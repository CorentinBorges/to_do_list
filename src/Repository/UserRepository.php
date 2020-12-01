<?php


namespace App\Repository;


use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\Uuid;

class UserRepository extends ServiceEntityRepository
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createAnonyme() : User
    {
        $user = new User();
        $user->setPassword('AnonymePass');
        $this->passwordEncoder->encodePassword($user, $user->getPassword());
        $user->setRoles('user');
        $user->setUsername('anonyme');
        $user->setEmail('anonyme@gmail.com');

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function findAnonyme()
    {
        return $this->findOneBy(['username' => 'anonyme']);
    }
}