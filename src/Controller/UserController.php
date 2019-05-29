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
use App\Security\JwtAutenticador;
use Firebase\JWT\JWT;

use JMS\Serializer\SerializerBuilder;

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

        if (is_null($dadosEmJson->email) || is_null($dadosEmJson->senha)){
            return new JsonResponse([
                'erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy(['email' => json_decode($dadosEmJson->email)]);
        if ($user) {
            return new JsonResponse(['Erro' => 'Este email já foi cadastrado'], 418);
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
        $dadosEmJson = json_decode($request->getContent());

        if (is_null($dadosEmJson->email) || is_null($dadosEmJson->senha)){
            return new JsonResponse([
                'erro' => 'Favor enviar usuário e senha'
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->findOneBy([
            'email' => $dadosEmJson->email
        ]);

        if ($user->getPassword() !== $dadosEmJson->senha){
            return new JsonResponse([
                'erro' => 'Usuário ou senha inválidos'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWT::encode(['id' => $user->getId()], $this->getParameter('auth_key'), 'HS256');

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

    public function startFollowing(Request $request): JsonResponse
    {
        $dadoEmJson = json_decode($request->getContent());

        $user = $this->factory->getUserByToken($request, $this->repository);
        $targetUser = $this->repository->find($dadoEmJson->id);

        if (!$targetUser) {
            throw $this->createNotFoundException(
                'No user found for id '.$dadosEmJson->id
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
        $serializer = SerializerBuilder::create()->build();

        $user = $this->factory->getUserByToken($request, $this->repository);

        $list = $serializer->serialize($user->getIsFollowedBy(), 'json');
        $followList = $serializer->serialize($user->getFollow(), 'json');

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
        try {
            $file = new BinaryFileResponse($this->getParameter('profile_image_upload_directory').'/'.$id.'.png');
        } catch (Exception $e) {
            return new Response('', Response::HTTP_NOT_FOUND);
        }
        return $file;
    }


}
