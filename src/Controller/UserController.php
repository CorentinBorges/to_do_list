<?php

namespace App\Controller;

use App\Cache\UserCache;
use App\Entity\User;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @var UserCache
     */
    private UserCache $userCache;

    public function __construct(UserCache $userCache)
    {
        $this->userCache = $userCache;
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        $users = $this->userCache->getList('users_list_' . $_SERVER['APP_ENV'], 259200);
        return $this->render('user/list.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/users/create", name="user_create")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['roleAdmin']->getData()) {
                $user->setRoles('admin');
            } else {
                $user->setRoles('user');
            }
            $em = $this->getDoctrine()->getManager();
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");
            $this->userCache->delete('users_list_' . $_SERVER['APP_ENV']);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function editAction(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(UserType::class, $user, ['is_edit' => true]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form['roleAdmin']->getData()) {
                $user->setRoles('admin');
            } else {
                $user->setRoles('user');
            }
            if ($form['password']->getData()) {
                $user->setPassword($form['password']->getData());
                $password = $passwordEncoder->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");
            $this->userCache->delete('users_list_' . $_SERVER['APP_ENV']);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }
}
