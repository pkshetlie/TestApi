<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Form\UserType;
use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/new", name="user_new", methods={"GET"})
     * @return Response
     */
    public function new(): Response
    {
        $user = new User();
        $urlToCall = $this->generateUrl('user_api_add');
        return $this->edit( $user, $urlToCall, 'POST');
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET"})
     * @param User $user
     * @param null $urlToCall
     * @param string $method
     * @param string $trigger
     * @return Response
     */
    public function edit(User $user, $urlToCall = null, $method = 'POST',$trigger = 'getlistUser'): Response
    {
        if (null === $urlToCall) {
            $urlToCall = $this->generateUrl('user_api_edit', ['id' => $user->getId()]);
        }

        $form = $this->createForm(UserType::class, $user, [
            'attr' => [
                'action' => $urlToCall,
                'method' => $method,
                'data-trigger'=>$trigger
            ]
        ]);

        return $this->render('front/_partial/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
