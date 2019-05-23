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

        return new JsonResponse([
            'email' => $user->getEmail(),
            'senha' => $user->getPassword()
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

}
