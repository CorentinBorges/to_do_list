<?php


namespace App\Tests\Controller;

use App\Entity\User;
use App\Tests\AbstractWebTestCase;

class UserControllerTest extends AbstractWebTestCase
{
    public function testEditUserWithNoUser ()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();
        $user = $this->createUser();

        $this->client->request('GET', '/users/'.$user->getId().'/edit');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testEditUserWithMissingDatas()
    {
        $admin=$this->createAdminUser();
        $user = $this->createUser();
        $this->logIn($admin);
        self::assertSame("userTest",$user->getUsername());
        $this->client->request('GET', '/users/'.$user->getId().'/edit');
        $this->client->submitForm('Modifier', [
            'user[username]' => 'newUsername',
            'user[password]' => 'testPass',
            'user[email]' => 'usercreategmail.com',
            'user[roleUser]' => true,
            'user[roleAdmin]' => false,
        ]);
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertNull(
            $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'newUsername']));
        self::assertStringContainsString("Le format de l&#039;adresse n&#039;est pas correcte.",$this->client->getResponse());
    }

    public function testEditUserWithoutAdmin()
    {
        $admin = $this->createAdminUser();
        $user=$this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/users/'.$admin->getId().'/edit');
        self::assertEquals(403,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('forbidden',$this->client->getResponse());
    }

    public function testEditUserWithAdmin()
    {
        $admin=$this->createAdminUser();
        $user = $this->createUser();
        $this->logIn($admin);
        self::assertSame("userTest",$user->getUsername());
        $this->client->request('GET', '/users/'.$user->getId().'/edit');
        $this->client->submitForm('Modifier', [
            'user[username]' => 'editUserTest',
            'user[password]' => 'testPass',
            'user[email]' => 'usercreate@gmail.com',
            'user[roleUser]' => true,
            'user[roleAdmin]' => false,
        ]);
        self::assertNotNull($this->entityManager->getRepository(User::class)->findOneBy(['username' => 'editUserTest']));
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/users');
    }

    public function testCreateUserWithWrongDatas()
    {
        $admin=$this->createAdminUser();
        $this->logIn($admin);
        $this->client->request('GET', '/users/create');
        $this->client->submitForm('Ajouter', [
            'user[username]' => 'userTest',
            'user[password][first]' => 'testPass',
            'user[password][second]' => 'testPass',
            'user[roleUser]' => true,
            'user[roleAdmin]' => false,
        ]);
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertNull(
            $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'userTest']));
        self::assertStringContainsString('Vous devez saisir une adresse email.',$this->client->getResponse());
    }

    public function testCreateUserWithNoUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $this->client->request('GET', '/users/create');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testCreateUserWithoutAdmin()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/users/create');
        self::assertEquals(403,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('forbidden',$this->client->getResponse());
    }

    public function testCreateUserWithAdmin()
    {
        $admin=$this->createAdminUser();
        $this->logIn($admin);
        $this->client->request('GET', '/users/create');
        $this->client->submitForm('Ajouter', [
            'user[username]' => 'userTest',
            'user[password][first]' => 'testPass',
            'user[password][second]' => 'testPass',
            'user[email]' => 'usercreate@gmail.com',
            'user[roleUser]' => true,
            'user[roleAdmin]' => false,
        ]);
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects('/users');
        self::assertNotNull(
            $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'userTest']));
    }

    public function testListActionWithNoUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $this->client->request('GET', '/users');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testListActionWithNotAdmin()
    {
        $user = $this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/users');
        self::assertEquals(403,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('forbidden',$this->client->getResponse());
    }

    public function testListActionWithAdmin()
    {
        $admin=$this->createAdminUser();
        $user = $this->createUser();
        $this->logIn($admin);
        $this->client->request('GET', '/users');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('userTest',$this->client->getResponse());

    }
}