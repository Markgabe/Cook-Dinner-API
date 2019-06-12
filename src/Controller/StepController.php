<?php

namespace App\Controller;

use App\Helper\RecipeFactory;
use App\Helper\UserFactory;
use App\Repository\RecipeRepository;
use App\Repository\StepRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class StepController {

    private $recipeRepository;
    private $recipeFactory;
    private $manager;
    private $repository;
    private $userFactory;
    private $userRepository;

    public function __construct( RecipeRepository $recipeRepository,
                                 RecipeFactory $recipeFactory,
                                 EntityManagerInterface $manager,
                                 StepRepository $repository,
                                 UserFactory $userFactory,
                                 UserRepository $userRepository )
    {
        $this->recipeRepository = $recipeRepository;
        $this->recipeFactory = $recipeFactory;
        $this->manager = $manager;
        $this->repository = $repository;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
    }

    public function addStep(Request $request, $id): JsonResponse
    {
        $recipe = $this->recipeRepository->find($id);
        if (!$recipe) return new JsonResponse('', Response::HTTP_NOT_FOUND);

        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        if (!($recipe->getUser() === $user)) return new JsonResponse('', Response::HTTP_UNAUTHORIZED);

        $step = $this->recipeFactory->addStep($request, $recipe);

        $this->manager->persist($step);
        $this->manager->flush();

        return new JsonResponse('', Response::HTTP_CREATED);
    }

    public function removeStep(Request $request, $id): JsonResponse
    {
        $step = $this->repository->find($id);

        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        if (!($step->getRecipe()->getUser()) === $user) return new JsonResponse('', Response::HTTP_UNAUTHORIZED);

        $this->manager->remove($step);
        $this->manager->flush();

        return new JsonResponse('', Response::HTTP_NO_CONTENT);
    }

}