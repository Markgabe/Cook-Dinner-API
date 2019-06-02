<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use JMS\Serializer\SerializerBuilder;
use App\Entity\Recipe;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RecipeRepository;
use App\Repository\UserRepository;
use App\Helper\ExtratorDadosRequest;
use App\Controller\UserController;
use App\Helper\UserFactory;

class RecipeController extends AbstractController
{

    protected $repository;
    protected $userRepository;
    protected $entityManager;
    protected $userFactory;
    private $requestDataExtractor;

    public function __construct(
        EntityManagerInterface $entityManager,
        RecipeRepository $repository,
        ExtratorDadosRequest $extratorDadosRequest,
        UserRepository $userRepository,
        UserFactory $userFactory
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->requestDataExtractor = $extratorDadosRequest;
        $this->userRepository = $userRepository;
        $this->userFactory = $userFactory;
    }

    public function newRecipe(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent());
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        $recipe = new Recipe();
        $recipe
            ->setName($jsonData->name)
            ->setDescription($jsonData->description)
            ->setUser($user)
            ->setCreatedAt()
            ->setTime(0);

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return new JsonResponse($recipe);
    } //Not fully implemented

    public function showRecipe($id): JsonResponse
    {
        $recipe = $this->repository->find($id);

        if (!$recipe) {
            return new JsonResponse('', Response::HTTP_NO_CONTENT);
        }

        return new JsonResponse($recipe);
    }

    public function listAll(Request $request): JsonResponse
    {
        $filter = $this->requestDataExtractor->buscaDadosFiltro($request);
        $informacoesDeOrdenacao = $this->requestDataExtractor->buscaDadosOrdenacao($request);
        [$paginaAtual, $itensPorPagina] = $this->requestDataExtractor->buscaDadosPaginacao($request);

        $list = $this->repository->findBy(
            $filter,
            $informacoesDeOrdenacao,
            $itensPorPagina,
            ($paginaAtual - 1) * $itensPorPagina
        );

        return new JsonResponse($list);
    }

    public function listUserRecipes(int $userId): JsonResponse
    {
        $recipeList = $this->repository->findBy([
            'user' => $this->userRepository->find($userId)
        ]);
        return new JsonResponse($recipeList);
    } //fully implemented

    public function deleteRecipe(Request $request, int $id): Response
    {
        $recipe = $this->repository->find($id);
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);
        if ($recipe->getUser()->getId() !== $user->getId()){
            return new Response('', Response::HTTP_UNAUTHORIZED);
        }
        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function recipesFromUsersYouFollow(Request $request): JsonResponse
    {
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);
        $followList = $user->getFollow();
        $idList = $this->listSerialize($followList, $this->userRepository);
        return new JsonResponse($idList);
    }

    public function listSerialize($list, $userRepo)
    {
            $newArray = array();
            foreach ( $list as $item ) {
                foreach ($item->getRecipes() as $recipe){
                    array_push($newArray, $recipe);
                }
            }

            return $newArray;
    }

}
