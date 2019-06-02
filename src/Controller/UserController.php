<?php

namespace App\Controller;

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

    public function __construct(
        EntityManagerInterface $manager,
        UserRepository $repository,
        UserFactory $factory
        ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->factory = $factory;
    }

    public function newUser(Request $request): JsonResponse
    {
        $dadosEmJson = json_decode($request->getContent());

        if (is_null($dadosEmJson->username) || is_null($dadosEmJson->password)){
            return new JsonResponse([
                'Erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy(['email' => $dadosEmJson->username]);
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

        if (!$user) {
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if ($user->getPassword() !== $jsonData->password){
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['id' => $user->getId()], $this->getParameter('auth_key'), 'HS256');

        return new JsonResponse([
            'access-token' => $token
        ]);

    }

    public function findEmailById($id): JsonResponse
    {
        $user = $this->repository->find($id);
        return new JsonResponse(["email" => $user->getEmail()]);
    }

    public function getCred(Request $request): JsonResponse
    {
        $cred = JwtAuthenticator::getCredentials($request);
        return new JsonResponse($cred);
    }

    public function startFollowing(Request $request): JsonResponse
    {
        $dadoEmJson = json_decode($request->getContent());

        $user = $this->factory->getUserByToken($request, $this->repository);
        $targetUser = $this->repository->find($dadoEmJson->id);

        if (!$targetUser) {
            throw $this->createNotFoundException(
                'No user found for id '.$dadoEmJson->id
            );
        }

        $user->addFollow($targetUser);
        $targetUser->addIsFollowedBy($user);

        $this->manager->flush();

        return new JsonResponse([
            'user'=> $user->getId(),
            'followedUser'=> $targetUser->getId()
        ]);

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
        if (!$file) {
            return new Response('', Response::HTTP_BAD_REQUEST);
        }
        $user = $this->factory->getUserByToken($request, $this->repository);
        try {
            $file->move($this->getParameter('profile_image_upload_directory'), $user->getId().'.png');
        } catch (Exception $e) {
            return new JsonResponse('', Response::UNSUPPORTED_MEDIA_TYPE);
        }
        return new Response('', Response::HTTP_CREATED);
    }

    public function getPicture($id): Response
    {
        if(file_exists($this->getParameter('profile_image_upload_directory').'/'.$id.'.png')){
            return new BinaryFileResponse($this->getParameter('profile_image_upload_directory').'/'.$id.'.png');
        }
        return new Response('', Response::HTTP_NOT_FOUND);
    }


}
