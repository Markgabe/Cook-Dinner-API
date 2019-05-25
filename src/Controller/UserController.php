<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Security\JwtAutenticador;
use Firebase\JWT\JWT;

class UserController extends AbstractController
{

    protected $manager;
    private $repository;

    public function __construct( EntityManagerInterface $manager, UserRepository $repository ) {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    public function newUser(Request $request): JsonResponse
    {
        $jsonRequest = json_decode($request->getContent());

        $user = new User();
        $user->setEmail($jsonRequest->email)
             ->setPassword($jsonRequest->senha);

        $this->manager->persist($user);
        $this->manager->flush();

        $token = JWT::encode(['email' => $user->getEmail()], 'MinhaChaveBolada', 'HS256');

        return new JsonResponse([
            'access-token' => $token
        ]);
    }

    public function findIdByEmail(Request $request): JsonResponse
    {
        $dadosEmJson = json_decode($request->getContent());
        $user = $this->repository->findOneBy([
            'email' => $dadosEmJson->email
        ]);
        return new JsonResponse(["Id" => $user->getId()]);
    }

    public function getCred(Request $request): JsonResponse
    {
        $cred = JwtAutenticador::getCredentials($request);
        return new JsonResponse($cred);
    }

    public function getUserByToken(Request $request, $repository)
    {
        $cred = JwtAutenticador::getCredentials($request);
        $user = $repository->findOneBy([
            'email' => $cred->email
        ]);
        return $user;
    }

    public function startFollowing(Request $request, $id): JsonResponse
    {
        $user = $this->getUserByToken($request, $this->repository);
        $targetUser = $this->repository->find($id);
        $user->addFollow($targetUser);
        $targetUser->addIsFollowedBy($user);
        return new JsonResponse([
            'user'=> $user->getId(),
            'followedUser'=> $targetUser->getId()
        ]);
    }

    public function getAllFollowers(Request $request): JsonResponse
    {
        $user = $this->getUserByToken($request, $this->repository);
        $list = $user->getIsFollowedBy();
        $this->manager->flush();
        return new JsonResponse([
            "lista" => $list,
            "user" => $user
        ]);
    }

}
