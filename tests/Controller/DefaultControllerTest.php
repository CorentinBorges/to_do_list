<?php

namespace App\Tests\Controller;

use App\Tests\AbstractWebTestCase;

class DefaultControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString('Bienvenue sur Todo List', $this->client->getResponse()->getContent());
    }


}
