<?php

namespace App\Controller;

use App\Helper\RecipeFactory;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StepController {

    private $recipeRepository;
    private $recipeFactory;
    private $manager;

    public function __construct(RecipeRepository $recipeRepository, RecipeFactory $recipeFactory, EntityManagerInterface $manager)
    {

        $this->recipeRepository = $recipeRepository;
        $this->recipeFactory = $recipeFactory;
        $this->manager = $manager;
    }

    public function addStep(Request $request, $id): JsonResponse
    {
        $recipe = $this->recipeRepository->find($id);
        if (!$recipe) return new JsonResponse('', Response::HTTP_NOT_FOUND);

        $step = $this->recipeFactory->addStep($request, $recipe);

        $this->manager->persist($step);
        $this->manager->flush();

        return new JsonResponse('', Response::HTTP_CREATED);
    }

}