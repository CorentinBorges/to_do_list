<?php


namespace App\Tests\Controller;


use App\Tests\AbstractWebTestCase;

class SecurityControllerTest extends AbstractWebTestCase
{

    public function testLoginActionPage()
    {
        $this->client->request('GET', '/login');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Mot de passe',$this->client->getResponse());
    }

}