<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;

use App\Entity\Receita;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ReceitaRepository;
use App\Helper\ExtratorDadosRequest;

use App\Controller\UserController;

class RecipeController extends AbstractController
{

    protected $repository;
    protected $entityManager;
    private $extratorDadosRequest;

    public function __construct(
        EntityManagerInterface $entityManager,
        ReceitaRepository $repository,
        ExtratorDadosRequest $extratorDadosRequest
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->extratorDadosRequest = $extratorDadosRequest;
    }

    public function novaReceita(Request $request): JsonResponse
    {
        $corpoRequisicao = $request->getContent();
        $dadoEmJson = json_decode($corpoRequisicao);

        $repositorio = $this->getDoctrine()->getRepository(User::class);
        $user = UserController::getUserByToken($request, $repositorio);

        $receita = new Receita();
        $receita
            ->setNome($dadoEmJson->Nome)
            ->setDescricao($dadoEmJson->Descricao)
            ->setUser($user);

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

    public function listaTodas(Request $request): JsonResponse
    {
        $filtro = $this->extratorDadosRequest->buscaDadosFiltro($request);
        $informacoesDeOrdenacao = $this->extratorDadosRequest->buscaDadosOrdenacao($request);
        [$paginaAtual, $itensPorPagina] = $this->extratorDadosRequest->buscaDadosPaginacao($request);

        $lista = $this->repository->findBy(
            $filtro,
            $informacoesDeOrdenacao,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );

        return new JsonResponse($lista);
    }

    public function listaReceitasUsuario(int $userId): JsonResponse
    {
        $repositorio = $this->getDoctrine()->getRepository(User::class);
        $recipeList = $this->repository->findBy([
            'user' => $repositorio->find($userId)
        ]);
        return new JsonResponse($recipeList);
    }

    public function removeReceita(int $id): Response
    {
        $entidade = $this->repository->find($id);
        $this->entityManager->remove($entidade);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

}
