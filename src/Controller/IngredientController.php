<?php

namespace App\Controller;

use App\Helper\RecipeFactory;
use App\Helper\UserFactory;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IngredientController extends AbstractController
{

    private $recipeRepository;
    private $userFactory;
    private $userRepository;
    private $recipeFactory;
    private $manager;

    public function __construct( RecipeRepository $recipeRepository,
                                 UserFactory $userFactory,
                                 UserRepository $userRepository,
                                 RecipeFactory $recipeFactory,
                                 EntityManagerInterface $manager )
    {
        $this->recipeRepository = $recipeRepository;
        $this->userFactory = $userFactory;
        $this->userRepository = $userRepository;
        $this->recipeFactory = $recipeFactory;
        $this->manager = $manager;
    }

    public function addIngredient(Request $request, $id): JsonResponse
    {
        $recipe = $this->recipeRepository->find($id);
        if (!$recipe) return new JsonResponse(['error' => 'recipe not found'], Response::HTTP_NOT_FOUND);

        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        if (!($recipe->getUser() === $user)) return new JsonResponse('', Response::HTTP_UNAUTHORIZED);

        $ingredient = $this->recipeFactory->addIngredient($request, $recipe);

        $this->manager->persist($ingredient);
        $this->manager->flush();

        return new JsonResponse('', Response::HTTP_CREATED);
    }
}
