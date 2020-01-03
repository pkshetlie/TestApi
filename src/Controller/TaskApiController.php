<?php
namespace App\Controller;


use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TaskApiController
 * @package App\Controller
 * @Route("/api/v1/task")
 */
class TaskApiController extends AbstractController
{
    /**
     * @Route("/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="task_api_get_list")
     * @param User $user
     * @param TaskRepository $taskRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(User $user, TaskRepository $taskRepository, SerializerInterface $serializer)
    {
        $alltasks = $taskRepository->createQueryBuilder('t')->where('t.user = :user')
            ->setParameter('user', $user)
            ->orderBy('t.createdAt', 'DESC')
            ->getQuery()->getResult();
        $alltasks = $serializer->serialize($alltasks, 'json');
        return new JsonResponse($alltasks, 200, [], true);
    }

    /**
     * @Route("/get/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="task_api_get_one")
     * @param Task $task
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getOne(Task $task,SerializerInterface $serializer)
    {
        $task = $serializer->serialize($task, 'json');
        return new JsonResponse($task,200,[],true);
    }

    /**
     * @Route("/{id}", methods={"POST"}, name="task_api_add")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function add(Request $request, User $user)
    {
        $task = new Task();
        $task->setUser($user);
        $task->setCreatedAt(new \DateTime());

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($task);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['error' => "Erreur lors de la création de la tâche"], 500);
    }

    /**
     * @Route("/edit/{id}",requirements={"id"="\d+"}, methods={"POST"}, name="task_api_edit")
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function edit(Request $request, Task $task)
    {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['error' => "Erreur lors de l'édition de la tâche"], 500);
    }

    /**
     * @Route("/delete/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="task_api_delete")
     * @param EntityManagerInterface $em
     * @param Task $task
     * @return JsonResponse
     */
    public function delete(EntityManagerInterface $em, Task $task)
    {
        try {
            $task->setUser(null);
            $em->remove($task);
            $em->flush();
            return new JsonResponse(['success' => true]);
        } catch (Exception $e) {
            return new JsonResponse(['errror' => "Erreur lors de la suppression d'une tâche."]);
        }
    }
}