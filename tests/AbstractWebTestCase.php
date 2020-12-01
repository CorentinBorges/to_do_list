<?php


namespace App\Tests;



use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

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
}