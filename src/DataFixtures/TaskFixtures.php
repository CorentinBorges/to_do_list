<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class TaskFixtures extends Fixture implements DependentFixtureInterface
{

    /**
     * @var Generator $faker
     */
    protected Generator $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            /**
             * @var User $user
             */
            $user = $this->getReference('normalUser');
            $task->setTitle("Tâche numéro " . $i);
            $task->setContent($this->faker->text(100));
            $task->setUser($user);
            if ($i < 6) {
                $task->toggle(true);
            }

            $manager->persist($task);
        }

        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            /**
             * @var User $adminUser
             */
            $adminUser = $this->getReference('adminUser');
            $task->setTitle("Tâche numéro " . ($i + 10));
            $task->setContent($this->faker->text(100));
            $task->setUser($adminUser);
            if ($i < 6) {
                $task->toggle(true);
            }
            $anonTask = new Task();
            $anonTask->setContent($this->faker->text(100));
            $anonTask->setTitle("Tâche numéro " . ($i + 20));
            if ($i < 6) {
                $anonTask->toggle(true);
            }

            $manager->persist($task);
            $manager->persist($anonTask);
        }
        $manager->flush();
    }

    /**
     * Load firsts fixtures
     *
     * @return string[]
     */
    public function getDependencies(): array
    {
        return array (UserFixtures::class);
    }
}
