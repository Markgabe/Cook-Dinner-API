<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Avaliacao;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AvaliacaoRepository;
use App\Repository\RecipeRepository;

class RateController extends AbstractController
{

    protected $recipeRepository;
    protected $repository;
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        AvaliacaoRepository $repository,
        RecipeRepository $recipeRepository
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->recipeRepository = $recipeRepository;
    }

    public function novaAvaliacao(Request $request): JsonResponse
    {
        $dadoEmJson = json_decode($request->getContent());

        $receita = $this->recipeRepository->find($dadoEmJson->recipeId);
        if (!$receita) {
            throw $this->createNotFoundException(
                'No recipe found for id '.$dadoEmJson->recipeId
            );
        }

        $avaliacao = new Avaliacao();
        $avaliacao
            ->setNota($dadoEmJson->Nota)
            ->setFavorito($dadoEmJson->Favorito)
            ->setReceita($receita);
        $receita
            ->addAvaliacao($avaliacao);

        $this->entityManager->persist($avaliacao);
        $this->entityManager->flush();

        return new JsonResponse($avaliacao);
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
            'Nota da receita' => $avaliacao->getNota(),
            'Favorito' => $avaliacao->getFavorito()
            ]);
    }

    public function ratesFromRecipe($id): JsonResponse
    {
        $repositorio = $this->getDoctrine()->getRepository(Recipe::class);
        $recipe = $repositorio->find($id);
        $rateList = $recipe->getAvaliacao();
        $rateArray = array();
        foreach($rateList as $rate){
            array_push($rateArray, $rate);
        }
        return new JsonResponse($rateArray);
    }

    public function listaTodas(): JsonResponse
    {
        $listaAvaliacoes = $this->repository->findAll();
        return new JsonResponse($listaAvaliacoes);
    }

}
