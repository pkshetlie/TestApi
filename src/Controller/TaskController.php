<?php
namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/task")
 */
class TaskController extends AbstractController
{

    /**
     * @Route("/{id}/new", name="task_new", methods={"GET"})
     * @param User $user
     * @return Response
     */
    public function new(User $user): Response
    {
        $task = new Task();
        $task->setUser($user);
        $urlToCall = $this->generateUrl('task_api_add',['id'=>$user->getId()]);
        return $this->edit( $task, $urlToCall, 'POST');
    }

    /**
     * @Route("/{id}/edit", name="task_edit", methods={"GET"})
     * @param Task $task
     * @param null $urlToCall
     * @param string $method
     * @param string $trigger
     * @return Response
     */
    public function edit(Task $task, $urlToCall = null, $method = 'PUT',$trigger = 'getlist'): Response
    {
        if (null === $urlToCall) {
            $urlToCall = $this->generateUrl('task_api_edit', ['id' => $task->getId()]);
        }

        $form = $this->createForm(TaskType::class, $task, [
            'attr' => [
                'action' => $urlToCall,
                'method' => $method,
                'data-trigger'=>$trigger,
                'data-data'=> $task->getUser()->getId()
            ]
        ]);

        return $this->render('front/_partial/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
