<?php


namespace App\Tests\Controller;

use App\Entity\Task;
use App\Tests\AbstractWebTestCase;

class TaskControllerTest extends AbstractWebTestCase
{
    public function testDeleteTask()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $task=$this->createTask($user);
        $findTask=$this->entityManager->getRepository(Task::class)->findOneBy(['title'=>'Test Task']);
        self::assertNotNull($findTask);
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        $findTask=$this->entityManager->getRepository(Task::class)->findOneBy(['title'=>'Test Task']);
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects("/tasks");
        self::assertNull($findTask);
    }

    public function testDeleteNotExistingTask()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/tasks/1500/delete');
        self::assertEquals(404,$this->client->getResponse()->getStatusCode());
    }

    public function testDeleteTaskWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $user=$this->createUser();
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/delete');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testToggleTaskWhenDone()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $task = $this->createTask($user,true);
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
        self::assertFalse($task->isDone());
    }

    public function testToggleTaskWhenUndone()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
        self::assertTrue($task->isDone());
    }

    public function testToggleTaskWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $user=$this->createUser();
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/toggle');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }
    
    public function testTaskEditWithUserAndWrongData()
    {
        $user = $this->createUser();
        $this->logIn($user);
        /**
         * @var Task $task
         */
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->client->submitForm('Modifier', []);

        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects("/tasks");
        $this->client->request('GET', '/tasks');
        self::assertStringContainsString('Test Task',$this->client->getResponse());
    }

    public function testTaskEditWithUserAndData()
    {
        $user = $this->createUser();
        $this->logIn($user);
        /**
         * @var Task $task
         */
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        $this->client->submitForm('Modifier', [
            'task[title]' => 'Best title modified test',
            'task[content]' => 'The content',
        ]);
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects("/tasks");
        $this->client->request('GET', '/tasks');
        self::assertStringContainsString('Best title modified test',$this->client->getResponse());
    }

    public function testTaskEditWithUser()
    {
        $user = $this->createUser();
        $this->logIn($user);
        /**
         * @var Task $task
         */
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Modifier',$this->client->getResponse());
    }

    public function testTaskEditWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();
        $user = $this->createUser();
        /**
         * @var Task $task
         */
        $task = $this->createTask($user);
        $this->client->request('GET', '/tasks/'.$task->getId().'/edit');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testTaskCreateActionWithUserAndWrongData()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/tasks/create');
        $this->client->submitForm('Ajouter', []);
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Vous devez saisir un titre',$this->client->getResponse());
        self::assertStringContainsString('Vous devez saisir du contenu.',$this->client->getResponse());
    }

    public function testTaskCreateActionWithUserAndData()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/tasks/create');
        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Best title test',
            'task[content]' => 'The content',
        ]);

        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects("/tasks");
        $this->client->request('GET', '/tasks');
        self::assertStringContainsString('Best title test',$this->client->getResponse());
    }

    public function testTaskCreateActionWithUser()
    {
        $user=$this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/tasks/create');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString('Title',$this->client->getResponse());
    }

    public function testListActionWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $this->client->request('GET', '/tasks');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testListActionWithUserAndNoTasks()
    {
        $user = $this->createUser();
        $this->logIn($user);
        $this->client->request('GET', '/tasks');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            "Il n'y a pas de tâches en cours",
                    $this->client->getResponse()
        );
    }

    public function testListActionWithAdminUserAndTask()
    {
        $user = $this->createAdminUser();
        $this->createTask($user);
        $this->createAnonymeTask();
        $this->logIn($user);
        $this->client->request('GET', '/tasks');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            "Content test",
            $this->client->getResponse()
        );
        self::assertStringContainsString(
            "Créé par anonyme",
            $this->client->getResponse()
        );
        self::assertStringContainsString(
            "Créé par adminUser",
            $this->client->getResponse()
        );
    }

    public function testListActionWithUserAndTask()
    {
        $user = $this->createUser();
        $this->createTask($user);
        $this->createTask($user, true,'Done task');
        $this->createAnonymeTask();
        $this->logIn($user);
        $this->client->request('GET', '/tasks');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            "Content test",
            $this->client->getResponse()
        );
        self::assertStringNotContainsString(
            "Créé par anonyme",
            $this->client->getResponse()
        );
        self::assertStringNotContainsString(
            "Done task",
            $this->client->getResponse()
        );
    }

    public function testListDoneClassWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $this->client->request('GET', '/tasksDone');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

    public function testListDoneWithUserAndNoTasks()
    {
        $user = $this->createUser();
        $this->logIn($user);

        $this->client->request('GET', '/tasksDone');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            "Il n'y a pas de tâches terminées.",
            $this->client->getResponse()
        );
    }

    public function testListDoneWithUserNoAdminAndTasks()
    {
        $user = $this->createUser();
        $this->logIn($user);
        $this->createTask($user,true);
        $this->createAnonymeTask();
        $this->createTask($user, false,'undone task');

        $this->client->request('GET', '/tasksDone');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            "Créé par userTest",
            $this->client->getResponse()
        );
        self::assertStringNotContainsString('undone task', $this->client->getResponse());
        self::assertStringNotContainsString('Créé par anonyme', $this->client->getResponse());
    }

    public function testListDoneWithAdminAndTasks()
    {
        $user = $this->createAdminUser();
        $this->logIn($user);
        $this->createTask($user,true);
        $this->createAnonymeTask(true);
        $this->createTask($user, false,'undone task');

        $this->client->request('GET', '/tasksDone');
        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(
            "Content test",
            $this->client->getResponse()
        );
        self::assertStringNotContainsString('undone task', $this->client->getResponse());
        self::assertStringContainsString('Créé par anonyme', $this->client->getResponse());
        self::assertStringContainsString('Créé par adminUser', $this->client->getResponse());
    }

    public function testTaskCreateActionWithoutUser()
    {
        $uriLogin = $this->client->request('GET', '/login')->getBaseHref();

        $this->client->request('GET', '/tasks/create');
        self::assertEquals(302,$this->client->getResponse()->getStatusCode());
        self::assertResponseRedirects($uriLogin);
    }

//    public function testTaskCreateActionWithDatas()
//    {
//        $this->client->request('get', '/tasks/create');
//        self::assertEquals(200,$this->client->getResponse()->getStatusCode());
//        self::assertStringContainsString('Title',$this->client->getResponse());
//    }



}