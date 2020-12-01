<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class TaskController extends AbstractController
{
//    /**
//     * @Route("/tasks", name="task_list")
//     */
//    public function listAction(UserRepository $userRepository,TaskRepository $taskRepository)
//    {
//        if ($this->getUser()) {
//            $task = $this->getUser()->getTasks();
//            if ($userRepository->findAnonyme()) {
//                /**
//                 * @var User $anonUser
//                 */
//                $anonUser = $userRepository->findAnonyme();
//                $task[] = $taskRepository->findOneBy(['user' => $anonUser]);
//            }
//        }else{
//            $task = new Task();
//        }
//
//        return $this->render('task/list.html.twig', [
//            'tasks' => $task]);
//
//    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(Security $security)
    {
        $taskRepo = $this->getDoctrine()->getRepository(Task::class);
        if ($this->getUser()) {
            $task = $this->getUser()->getTasks();
            if ($security->isGranted('ROLE_ADMIN')) {
                foreach ($taskRepo->findBy(['user' => null]) as $newTask) {
                    $task->add($newTask);
                }
            }
        }else{
            $task = new Task();
        }

        return $this->render('task/list.html.twig', [
            'tasks' => $task]);

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

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request)
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

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

        return $this->redirectToRoute('task_list');
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

        return $this->redirectToRoute('task_list');
    }
}
