<?php

namespace App\Tests\Controller;

use App\Tests\AbstractWebTestCase;

class DefaultControllerTest extends AbstractWebTestCase
{
    public function testIndexWithoutUser()
    {
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Bienvenue sur Todo List', $this->client->getResponse()->getContent());
        $this->assertStringNotContainsString(
            'Créer une nouvelle tâche',
            $this->client->getResponse()->getContent());
    }

    public function testIndexWithUser()
    {
        $user = $this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Bienvenue sur Todo List', $this->client->getResponse()->getContent());
        $this->assertStringContainsString(
            'Créer une nouvelle tâche',
            $this->client->getResponse()->getContent());
    }

}
