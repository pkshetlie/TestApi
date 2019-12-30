<?php


namespace App\Controller;


use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\VarDumper\VarDumper;

class TaskApiController extends AbstractController
{
    /**
     * @Route("/api/v1/tasks/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="task_get_list")
     * @param User $user
     * @param TaskRepository $taskRepository
     * @param Serializer $serializer
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
     * @Route("/api/v1/task/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="task_get_one")
     * @param Task $task
     */
    public function getOne(Task $task)
    {
        return new JsonResponse($task);
    }


    /**
     * @Route("/api/v1/task/", methods={"POST"}, name="task_add")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TaskRepository $taskRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function add(Request $request, UserRepository $userRepository, TaskRepository $taskRepository, SerializerInterface $serializer)
    {
        $title = $request->get('title', false);
        $description = $request->get('description', false);
        $user_id = $request->get('user', false);
        $status = $request->get('status', false);


        if (!$title || !$description || !$status || !$user_id) {
            return new JsonResponse(['error' => "Il manque des informations"], 500);
        }
        $user = $userRepository->find($user_id);
        if ($user === null) {
            return new JsonResponse(['error' => "L'utilisateur est innexistant"], 500);
        }
        try {

            $task = new Task();
            $task->setTitle($title);
            $task->setDescription($description);
            $task->setStatus(Task::Status[$status]);
            $task->setUser($user);
            $task->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $user->addTask($task);
            $em->flush();
        } catch (\Exception $e) {
            VarDumper::dump($e->getMessage());
            return new JsonResponse(['error' => "Erreur lors de l'ecriture en base de données"], 500);
        }

        return $this->list($user, $taskRepository,$serializer);
    }

    /**
     * @Route("/api/v1/task/", methods={"PUT"}, name="task_edit")
     * @param UserRepository $userRepository
     */
    public function edit(UserRepository $userRepository)
    {

    }

    /**
     * @Route("/api/v1/task/{id}", methods={"DELETE"}, requirements={"id"="\d+"}, name="task_delete")
     * @param UserRepository $userRepository
     */
    public function delete(UserRepository $userRepository)
    {

    }
}