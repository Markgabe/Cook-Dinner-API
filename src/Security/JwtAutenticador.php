<?php

namespace App\Security;

use App\Repository\UserRepository;
use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class JwtAutenticador extends AbstractGuardAuthenticator
{

    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function start(Request $request, AuthenticationException $authException = null){}

    public function supports(Request $request)
    {
        return (
            $request->getPathInfo() !== '/login' 
            && $request->getPathInfo() !== '/sign_up'
            &&(strpos($request->getPathInfo(), '/find') === false)
            && (strpos($request->getPathInfo(), '/getpic') === false)
        );
    }

    public function getCredentials(Request $request)
    {
        $token = str_replace(
            'Bearer ',
            '',
            $request->headers->get('Authorization')
        );

        try {
            return JWT::decode($token, 'ecfca2f9c99031a5b7640485b478dd718b4e1d1aabe0631099320c88a7d215b4', ['HS256']);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!is_object($credentials) || !property_exists($credentials, 'id')) {
            return null;
        }
        $id = $credentials->id;
        return $this->repository->findOneBy(['id' => $id]);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return is_object($credentials) && property_exists($credentials, 'id');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse([
            'erro' => 'Falha na autenticação'
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function supportsRememberMe()
    {
        return false;
    }
}