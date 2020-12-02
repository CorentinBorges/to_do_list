<?php

namespace App\Controller;

use App\Cache\TaskCache;
use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

/**
 * Class TaskController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class TaskController extends AbstractController
{
    //todo: edit audit with butons

    private TaskCache $taskCache;

    public function __construct(TaskCache $taskCache)
    {
        $this->taskCache = $taskCache;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user) {
            $tasks = $this->taskCache->getList(
                'task_list_' . $_SERVER['APP_ENV'].'_'.$user->getUsername(),
                259200,
                false,
                $user
            );
        }else{
            $tasks = [];
        }
        $page = 'list_not_done';


        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'user' => $user,
            'page' => $page
        ]);

    }

    /**
     * @Route("/tasksDone", name="task_done")
     */
    public function listDoneTasks()
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();

        if ($user) {
            $tasks = $this->taskCache->getList(
                'task_list_done_' . $_SERVER['APP_ENV'].'_'.$user->getUsername(),
                259200,
                true,
                $user
            );
        }else{
            $tasks = [];
        }

        $page = 'list_done';

        return $this->render('task/list.html.twig', [
            'tasks' => $tasks,
            'user' => $user,
            'page' => $page
        ]);
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request, UserRepository $userRepository)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($this->getUser()) {
                /**
                 * @var User $user
                 */
                $user = $this->getUser();
            }else{
                if ($userRepository->findOneBy(['username' => 'anonyme'])) {
                    $user = $userRepository->findOneBy(['username' => 'anonyme']);
                }else{
                    $user = $userRepository->createAnonyme();
                }
            }
            $task->setUser($user);

            $em->persist($task);
            $em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');
            $this->taskCache->deleteCache('task_list_' . $_SERVER['APP_ENV'].'_'.$user->getUsername());

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request, Security $security)
    {
        /**
         * @var User $userTask
         */
        $userTask = $task->getUser();
        if (!$security->isGranted('ROLE_ADMIN')) {
            if ($this->getUser() !== $userTask ) {
                throw new AccessDeniedHttpException('Vous ne pouvez pas modifier cette tâche');
            }
        } elseif($userTask->getUsername() !== 'anonyme') {
            if ($this->getUser() !== $userTask ) {
                throw new AccessDeniedHttpException('Vous ne pouvez pas modifier cette tâche');
            }
        }


        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');
            $this->taskCache->deleteCache(
                'task_list_' . $_SERVER['APP_ENV'].'_'.$this->getUser()->getUsername());
            $this->taskCache->deleteCache(
                'task_list_done_' . $_SERVER['APP_ENV'].'_'.$this->getUser()->getUsername());



            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task)
    {
        $task->toggle(!$task->isDone());
        $this->getDoctrine()->getManager()->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        $this->taskCache->deleteCache(
            'task_list_done_' . $_SERVER['APP_ENV'].'_'.$this->getUser()->getUsername());
        $this->taskCache->deleteCache(
            'task_list_' . $_SERVER['APP_ENV'].'_'.$this->getUser()->getUsername());
        if ($task->isDone()){
            return $this->redirectToRoute('task_list');
        } else{
            return $this->redirectToRoute('task_done') ;
        }
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($task);
        $em->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');
        $this->taskCache->deleteCache(
            'task_list_' . $_SERVER['APP_ENV'].'_'.$this->getUser()->getUsername());
        return $this->redirectToRoute('task_list');
    }
}
