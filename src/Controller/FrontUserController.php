<?php
namespace App\Controller;


use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FrontUserController extends AbstractController
{
    /**
     * @Route("users",name="front_users")
     * @param UserRepository $userRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(UserRepository $userRepository)
    {
        return $this->render("front/user/index.html.twig", [
            'users' => $userRepository->createQueryBuilder('u')->orderBy('u.id','desc')->getQuery()->getResult()
        ]);
    }

}