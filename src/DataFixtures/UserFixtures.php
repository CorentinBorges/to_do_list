<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('Jhon Doe');
        $user->setPassword($this->passwordEncoder->encodePassword($user,'userPass'));
        $user->setEmail('user@gmail.com');
        $user->setRoles("user");
        $this->setReference( 'normalUser', $user);
        $manager->persist($user);

        $adminUser = new User();
        $adminUser->setUsername('Admin');
        $adminUser->setPassword($this->passwordEncoder->encodePassword($adminUser,'adminPass'));
        $adminUser->setEmail('userAdmin@gmail.com');
        $adminUser->setRoles("admin");
        $this->setReference('adminUser', $adminUser);
        $manager->persist($adminUser);

        $manager->flush();

    }
}