<?php


namespace App\Tests\Controller;


use App\Tests\AbstractWebTestCase;

class TaskControllerTest extends AbstractWebTestCase
{
    public function testListActionWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $this->client->request('GET', '/tasks');
        self::assertResponseRedirects($uriLogin);
    }
}