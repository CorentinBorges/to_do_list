<?php


namespace App\Tests\Controller;


use App\Tests\AbstractWebTestCase;

class TestControllerTest extends AbstractWebTestCase
{
    public function testListActionWithoutUser()
    {
        $this->client->request('GET', '/tasks');
        self::assertStringContainsString();
    }
}