<?php


namespace App\Tests;



use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

abstract class AbstractWebTestCase extends WebTestCase
{
    /**
     * @var KernelBrowser
     */
    protected $client;
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    /**
     * @var ContainerInterface
     */
    protected $containerService;
    /**
     * @var EncoderFactory
     */
    protected $encoderFactory;


    protected function setUp() : void
    {
        $this->client = static::createClient();
        $this->containerService = self::$container;

        $this->entityManager = $this->containerService->get('doctrine.orm.entity_manager');
        $this->encoderFactory = $this->containerService->get('security.encoder_factory');

        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($this->entityManager->getMetadataFactory()->getAllMetadata());
    }

    public function createUser()
    {
        $user = new User();
        $user->setUsername('userTest');
        $user->setEmail("letest@gmail.com");
        $password = $this->encoderFactory
            ->getEncoder(User::class)
            ->encodePassword('testPass','');
        $user->setPassword($password);
        $user->setRoles('user');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function createAdminUser()
    {
        $user = new User();
        $user->setUsername('adminUser');
        $user->setEmail("user@gmail.com");
        $password = $this->encoderFactory
            ->getEncoder(User::class)
            ->encodePassword('testAdmin','');
        $user->setPassword($password);
        $user->setRoles('admin');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function createAnonymeUser()
    {
        $user = new User();
        $user->setUsername('anonyme');
        $user->setEmail("anonyme@gmail.com");
        $password = $this->encoderFactory
            ->getEncoder(User::class)
            ->encodePassword('testPass','');
        $user->setPassword($password);
        $user->setRoles('user');
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function createAnonymeTask(bool $isDone=null)
    {
        $user = $this->createAnonymeUser();
        $task = new Task();
        $task->setUser($user);
        $task->setTitle('Test Task');
        $task->setContent('Content test');
        if ($isDone) {
            $task->toggle($isDone);
        }
        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $task;
    }

    public function createTask(User $user, bool $isDone=false, string $title=null, string $content=null)
    {
        $task = new Task();
        $task->setUser($user);
        $title === null ? $task->setTitle('Test Task') : $task->setTitle($title);
        $content === null ? $task->setContent('Content test') : $task->setContent($content);
        $task->toggle($isDone);

        $this->entityManager->persist($task);
        $this->entityManager->flush();
        return $task;
    }

    public function logIn(User $user)
    {
        $session = self::$container->get('session');

        // somehow fetch the user (e.g. using the user repository)

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'main';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

}