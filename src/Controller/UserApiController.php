<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserApiController
 * @package App\Controller
 * @Route("/api/v1/user")
 */
class UserApiController extends AbstractController
{
    /**
     * @Route("/", methods={"GET"}, requirements={"id"="\d+"}, name="user_api_get_list")
     * @param User $user
     * @param UserRepository $userRepository
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function list(UserRepository $userRepository, SerializerInterface $serializer)
    {
        $allUsers = $userRepository->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery()->getResult();
        $allUsers = $serializer->serialize($allUsers, 'json');
        return new JsonResponse($allUsers, 200, [], true);
    }

    /**
     * @Route("/get/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="user_api_get_one")
     * @param User $user
     * @param SerializerInterface $serializer
     * @return JsonResponse
     */
    public function getOne(User $user,SerializerInterface $serializer)
    {
        $user = $serializer->serialize($user, 'json');
        return new JsonResponse($user,200,[],true);
    }

    /**
     * @Route("/", methods={"POST"}, name="user_api_add")
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function add(Request $request)
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return new JsonResponse(['success' => true]);
        }

        return new JsonResponse(['error' => "Erreur lors de la création de l'utilisateur"], 500);
    }

//    /**
//     * @Route("/edit/{id}",requirements={"id"="\d+"}, methods={"POST"}, name="user_api_edit")
//     * @param Request $request
//     * @param User $user
//     * @return JsonResponse
//     */
//    public function edit(Request $request, User $user)
//    {
//        $form = $this->createForm(UserType::class, $user);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $this->getDoctrine()->getManager()->flush();
//            return new JsonResponse(['success' => true]);
//        }
//
//        return new JsonResponse(['error' => "Erreur lors de l'édition de la tache"], 500);
//    }

    /**
     * @Route("/delete/{id}", methods={"GET"}, requirements={"id"="\d+"}, name="user_api_delete")
     * @param EntityManagerInterface $em
     * @param User $user
     * @return JsonResponse
     */
    public function delete(EntityManagerInterface $em, User $user)
    {
        try {
            foreach($user->getTasks() AS $task){
                $user->removeTask($task);
                $em->remove($task);
            }
            $em->remove($user);
            $em->flush();
            return new JsonResponse(['success' => true]);
        } catch (Exception $e) {
            return new JsonResponse(['errror' => "Erreur lors de la suppression d'un utilisateur."]);
        }
    }
}