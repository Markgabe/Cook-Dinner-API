<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class LoginController extends AbstractController
{
    private $repository;
    private $enconder;

    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder
    ){
        $this->repository = $repository;
        $this->encoder = $encoder;
    }

    /**
     * @Route("/login", name="login")
     */
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

        $token = JWT::encode(['email' => $user->getEmail()], 'MinhaChaveBolada', 'HS256');

        return new JsonResponse([
            'access-token' => $token
        ]);

    }
}
