<?php

namespace App\Controller;

use App\Helper\RateFactory;
use App\Helper\RecipeFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
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

    protected $entityManager;
    protected $repository;
    protected $userRepository;
    protected $factory;
    protected $userFactory;
    protected $rateFactory;
    private $requestDataExtractor;

    public function __construct(
        EntityManagerInterface $entityManager,
        RecipeRepository $repository,
        UserRepository $userRepository,
        RecipeFactory $recipeFactory,
        UserFactory $userFactory,
        RateFactory $rateFactory,
        ExtratorDadosRequest $extratorDadosRequest
        ) {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->factory = $recipeFactory;
        $this->userFactory = $userFactory;
        $this->rateFactory = $rateFactory;
        $this->requestDataExtractor = $extratorDadosRequest;
    }

    public function createRecipe(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent());
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        $recipe = $this->recipeFactory($request)->setUser($user);

        $this->entityManager->persist($recipe);
        $this->entityManager->flush();

        return new JsonResponse($recipe);
    } //Not fully implemented

    public function showRecipe($id): JsonResponse
    {
        $recipe = $this->repository->find($id);

        if (!$recipe) return new JsonResponse('', Response::HTTP_NO_CONTENT);

        return new JsonResponse($recipe);
    }

    public function updateRecipe(Request $request): JsonResponse
    {
        $recipe = $this->repository->find($jsonData->id);

        $recipe = $this->factory->updateRecipe($request, $recipe);

        $this->entityManager->flush();

        return new JsonResponse('', Response::HTTP_OK);
    }

    public function deleteRecipe(Request $request, int $id): Response
    {
        $recipe = $this->repository->find($id);
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        if ($recipe->getUser()->getId() !== $user->getId()) return new Response('', Response::HTTP_UNAUTHORIZED);

        $this->entityManager->remove($recipe);
        $this->entityManager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
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

    public function recipesFromUsersYouFollow(Request $request): JsonResponse
    {
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);
        $followList = $user->getFollow();
        $idList = $this->factory->listSerialize($followList);
        return new JsonResponse($idList);
    }

    public function storePicture(Request $request, $id): Response
    {
        $file = $request->files->get('picture');
        if (!$file) return new Response('', Response::HTTP_BAD_REQUEST);

        $recipe = $this->repository->find($id);
        try {
            $file->move($this->getParameter('recipe_image_upload_directory'), $recipe->getId().'.png');
        } catch (Exception $e) {
            return new JsonResponse('', Response::UNSUPPORTED_MEDIA_TYPE);
        }
        return new Response('', Response::HTTP_CREATED);
    }

    public function getPicture($id): Response
    {
        if(file_exists($this->getParameter('recipe_image_upload_directory').'/'.$id.'.png'))
            return new BinaryFileResponse($this->getParameter('recipe_image_upload_directory').'/'.$id.'.png');
        return new Response('', Response::HTTP_NOT_FOUND);
    }

    public function rateRecipe(Request $request, $id): JsonResponse
    {
        $recipe = $this->repository->find($id);

        $rate = $this->rateFactory->createRate($request)->setRecipe($recipe);
        $recipe->addRate($rate);

        $this->entityManager->persist($rate);
        $this->entityManager->flush();

        return new JsonResponse("", Response::HTTP_OK);
    }

    public function updateRate(Request $request, $id): JsonResponse
    {

    }

}
