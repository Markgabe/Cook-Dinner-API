<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Avaliacao;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\AvaliacaoRepository;
use App\Repository\ReceitaRepository;

class AvaliacaoController extends AbstractController
{

    protected $recipeRepository;
    protected $repository;
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        AvaliacaoRepository $repository,
        ReceitaRepository $recipeRepository
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->recipeRepository = $recipeRepository;
    }

    public function novaAvaliacao(Request $request): JsonResponse
    {
        $corpoRequisicao = $request->getContent();
        $dadoEmJson = json_decode($corpoRequisicao);

        $receita = $this->recipeRepository->find($dadoEmJson->RID);
        if (!$receita) {
            throw $this->createNotFoundException(
                'No recipe found for id '.$id
            );
        }

        $avaliacao = new Avaliacao();
        $avaliacao
            ->setNota($dadoEmJson->Nota)
            ->setFavorito($dadoEmJson->Favorito)
            ->setReceita($receita);

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

    public function listaTodas(): JsonResponse
    {
        $listaAvaliacoes = $this->repository->findAll();
        return new JsonResponse($listaAvaliacoes);
    }

}
