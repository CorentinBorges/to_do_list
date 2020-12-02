<?php


namespace App\Tests\Controller;


use App\Tests\AbstractWebTestCase;

class TaskControllerTest extends AbstractWebTestCase
{
    public function testListActionWithoutUser()
    {
        $this->client->request('GET', '/tasks');
    }
}