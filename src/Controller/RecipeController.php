<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Receita;
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

    public function novaReceita(Request $request): JsonResponse
    {
        $corpoRequisicao = $request->getContent();
        $dadoEmJson = json_decode($corpoRequisicao);

        $receita = new Receita();
        $receita
            ->setNome($dadoEmJson->Nome)
            ->setDescricao($dadoEmJson->Descricao);

        $this->entityManager->persist($receita);
        $this->entityManager->flush();

        return new JsonResponse($receita);
    }

    public function mostraReceita($id): JsonResponse
    {
        $receita = $this->repository->find($id);

        if (!$receita) {
            throw $this->createNotFoundException(
                'No recipe found for id '.$id
            );
        }

        return new JsonResponse($receita);
    }

    public function listaTodas(): JsonResponse
    {
        $listaReceitas = $this->repository->findAll();
        return new JsonResponse($listaReceitas);
    }

}
