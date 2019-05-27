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

use JMS\Serializer\SerializerBuilder;

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

    public function startFollowing(Request $request): JsonResponse
    {
        $dadoEmJson = json_decode($request->getContent());

        $user = $this->getUserByToken($request, $this->repository);
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

        $user = $this->getUserByToken($request, $this->repository);
        $list = $user->getIsFollowedBy();
        $list = $serializer->serialize($list, 'json');
        $followList = $user->getFollow();
        $followList = $serializer->serialize($followList, 'json');
        return new JsonResponse([
            "seguidores" => $this->listSerialize($list),
            "segue" => $this->listSerialize($followList),
            "user" => $user
        ]);
    }

    public function listSerialize($lis)
    {
            $newArray = array();
            $list = json_decode($lis);
            foreach ( $list as $item ) {
                $new = (object) ['id' => $item->id, 'email' => $item->email];
                array_push($newArray, $new);
            }

            return $newArray;
    }

}
