<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Avaliacao;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReceitaRepository;

class RecipeController extends AbstractController
{

    protected $repository;
    protected $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReceitaRepository $repository
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }

    public function novaAvaliacao(Request $request): JsonResponse
    {
        $corpoRequisicao = $request->getContent();
        $dadoEmJson = json_decode($corpoRequisicao);

        $avaliacao = new Avaliacao();
        $avaliacao
            ->setNota($dadoEmJson->Nota)
            ->setFavorito($dadoEmJson->Favorito);

        $this->entityManager->persist($avaliacao);
        $this->entityManager->flush();

        return new JsonResponse([
            'ID' => $avaliacao->getId()
        ]);
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
