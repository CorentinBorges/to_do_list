<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        if (!$this->userRepository->findAnonyme() && $this->getUser() && $this->isGranted("ROLE_ADMIN")) {
            $this->userRepository->createAnonyme();
        }
        return $this->render('default/index.html.twig',['user'=>$this->getUser()]);
    }
}
