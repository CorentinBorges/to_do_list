<?php


namespace App\Tests\Controller;


use App\Tests\AbstractWebTestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends AbstractWebTestCase
{
    public function testLoginAction()
    {

        $this->client->request('GET', '/login');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Mot de passe',$this->client->getResponse());
    }

}