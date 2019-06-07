<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Rate;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RateRepository;
use App\Repository\RecipeRepository;
use App\Helper\RateFactory;

class RateController extends AbstractController
{

    protected $recipeRepository;
    protected $repository;
    protected $entityManager;
    private $factory;

    public function __construct(
        EntityManagerInterface $entityManager,
        RateRepository $repository,
        RecipeRepository $recipeRepository,
        RateFactory $rateFactory
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->recipeRepository = $recipeRepository;
        $this->factory = $rateFactory;
    }

    public function newRate(Request $request, $recipe_id): JsonResponse
    {
        $recipe = $this->recipeRepository->find($recipe_id);
        if (!$recipe) return new JsonResponse('', Response::HTTP_NOT_FOUND);

        $rate = $this->factory->newRate($request);
        $rate->setRecipe($recipe);

        $recipe->addRate($rate);

        $this->entityManager->persist($rate);
        $this->entityManager->flush();

        return new JsonResponse();
    }

    public function mostraAvaliacao($id): JsonResponse
    {
        $avaliacao = $this->repository->find($id);

        if (!$avaliacao) {
            throw $this->createNotFoundException(
                'No avaliacao found for id '.$id
            );
        }
        return new JsonResponse([
            'Nota da receita' => $avaliacao->getGrade(),
            'Favorito' => $avaliacao->getFavorite()
            ]);
    }

    public function ratesFromRecipe($id): JsonResponse
    {
        $recipe = $this->recipeRepository->find($id);
        $rateList = $recipe->getRates();
        $rateArray = array();
        foreach($rateList as $rate){
            array_push($rateArray, $rate);
        }
        return new JsonResponse($rateArray);
    }

    public function showRate($id): JsonResponse
    {
        $rate = $this->repository->find($id);

        return new JsonResponse($rate);
    }

}
