<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;

class UserController extends AbstractController
{

    protected $manager;

    public function __construct( EntityManagerInterface $manager ) {
        $this->manager = $manager;
    }

    /**
     * @Route("/sign_in", name="new_user")
     */
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
}
