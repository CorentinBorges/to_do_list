<?php


namespace App\Tests\Controller;


use App\Tests\AbstractWebTestCase;

class SecurityControllerTest extends AbstractWebTestCase
{
    public function testLoginActionPageWithWrongUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username']='userTest';
        $form['_password']='testPass';

        $this->client->submit($form);

        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testLoginActionPageWithGoodData()
    {
        $uriHome = $this->client->request('GET', '/')->getBaseHref();

        $this->createUser();
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();

        $form['_username']='userTest';
        $form['_password']='testPass';

        $this->client->submit($form);
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriHome);
    }

    public function testLoginActionPage()
    {
        $this->client->request('GET', '/login');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Mot de passe',$this->client->getResponse());
    }

}