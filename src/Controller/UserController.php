<?php

namespace App\Controller;

use App\Helper\FileHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

use App\Helper\UserFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use App\Security\JwtAuthenticator;
use Firebase\JWT\JWT;

class UserController extends AbstractController
{

    protected $manager;
    protected $factory;
    private $repository;
    private $fileHandler;

    public function __construct(
        EntityManagerInterface $manager,
        UserRepository $repository,
        UserFactory $factory,
        FileHandler $fileHandler
        ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->fileHandler = $fileHandler;
    }

    public function newUser(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent());

        if (is_null($jsonData->username) || is_null($jsonData->password)){
            return new JsonResponse([
                'Erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy(['email' => $jsonData->username]);
        if ($user) {
            return new JsonResponse(['erro' => 'Este email já foi cadastrado'], 418);
        }

        $user = $this->factory->newUser($request);
        $this->manager->persist($user);
        $this->manager->flush();

        $token = JWT::encode(['id' => $user->getId()], $this->getParameter('auth_key'), 'HS256');

        return new JsonResponse([
            'access-token' => $token
        ]);
    }

    public function showUser($id): JsonResponse
    {
        $user = $this->repository->find($id);
        return new JsonResponse($user);
    }

    public function updateUser(Request $request): Response
    {
        $user = $this->factory->getUserByToken($request, $this->repository);
        if (!$user) return new JsonResponse('', Response::HTTP_NOT_FOUND);

        $user = $this->factory->updateUser($request, $user);

        $this->manager->flush();

        return new JsonResponse();
    }

    public function deleteUser(Request $request): Response
    {
        $user = $this->factory->getUserByToken($request, $this->repository);

        $this->manager->remove($user);
        $this->manager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function login(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent());

        if (is_null($jsonData->username) || is_null($jsonData->password)){
            return new JsonResponse([
                'erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy([
            'email' => $jsonData->username
        ]);

        if (!$user || ($user->getPassword() !== $jsonData->password)){
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['id' => $user->getId()], $this->getParameter('auth_key'), 'HS256');

        return new JsonResponse([
            'access-token' => $token
        ]);

    }

    public function getCred(Request $request): JsonResponse
    {
        $cred = JwtAuthenticator::getCredentials($request);
        return new JsonResponse($cred);
    }

    public function startFollowing(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent());

        $user = $this->factory->getUserByToken($request, $this->repository);
        $targetUser = $this->repository->find($jsonData->id);

        if (!$targetUser) {
            return new JsonResponse(["error" => "No user found for this id"], Response::HTTP_NOT_FOUND);
        }

        $user->addFollow($targetUser);
        $targetUser->addIsFollowedBy($user);

        $this->manager->flush();

        return new JsonResponse("");

    }

    public function getAllFollowers(Request $request): JsonResponse
    {
        $user = $this->factory->getUserByToken($request, $this->repository);

        $list = $user->getIsFollowedBy();
        $followList = $user->getFollow();

        return new JsonResponse([
            "seguidores" => $this->factory->listSerialize($list),
            "segue" => $this->factory->listSerialize($followList)
        ]);
    }

    public function storePicture(Request $request): Response
    {
        $file = $request->files->get('picture');
        if (!$file) return new Response('', Response::HTTP_BAD_REQUEST);

        $user = $this->factory->getUserByToken($request, $this->repository);

        $fileName = $this->fileHandler->uploadFile($file, '/profile');

        $user->setProfilePicture($fileName);

        $this->manager->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    public function getPicture($id): Response
    {
        $user = $this->repository->find($id);

        $fileName = $user->getProfilePicture();

        $fileResponse = $this->fileHandler->downloadFile($fileName, '/profile');

        return $fileResponse;
    }


}
