<?php

namespace App\Controller;

use App\Helper\FileHandler;
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
use App\Helper\RequestDataExtractor;
use App\Controller\UserController;
use App\Helper\UserFactory;

class RecipeController extends AbstractController
{

    protected $manager;
    protected $repository;
    protected $userRepository;
    protected $factory;
    protected $userFactory;
    protected $rateFactory;
    private $requestDataExtractor;
    private $fileHandler;

    public function __construct(
        EntityManagerInterface $manager,
        RecipeRepository $repository,
        UserRepository $userRepository,
        RecipeFactory $factory,
        UserFactory $userFactory,
        RateFactory $rateFactory,
        RequestDataExtractor $extratorDadosRequest,
        FileHandler $fileHandler
        ) {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->factory = $factory;
        $this->userFactory = $userFactory;
        $this->rateFactory = $rateFactory;
        $this->requestDataExtractor = $extratorDadosRequest;
        $this->fileHandler = $fileHandler;
    }

    public function createRecipe(Request $request): JsonResponse
    {
        $jsonData = json_decode($request->getContent());
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        $recipe = $this->factory->newRecipe($request)->setUser($user);

        $this->manager->persist($recipe);
        $this->manager->flush();

        return new JsonResponse($recipe);
    } //Not fully implemented

    public function showRecipe($id): JsonResponse
    {
        $recipe = $this->repository->find($id);

        if (!$recipe) return new JsonResponse('', Response::HTTP_NO_CONTENT);

        return new JsonResponse($recipe);
    }

    public function updateRecipe(Request $request, $id): Response
    {
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);
        $recipe = $this->repository->find($id);

        if ($recipe->getUser() !== $user) return new Response('',Response::HTTP_UNAUTHORIZED);

        $recipe = $this->factory->updateRecipe($request, $recipe);

        $this->manager->flush();

        return new Response(['id' => $recipe->getId()], Response::HTTP_OK);
    }

    public function deleteRecipe(Request $request, int $id): Response
    {
        $recipe = $this->repository->find($id);
        $user = $this->userFactory->getUserByToken($request, $this->userRepository);

        if ($recipe->getUser()->getId() !== $user->getId()) return new Response('', Response::HTTP_UNAUTHORIZED);

        $this->manager->remove($recipe);
        $this->manager->flush();

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    public function listAll(Request $request): JsonResponse
    {
        if (array_key_exists('search', $request->query->all()))
            return new JsonResponse($this->factory->search($request, $this->repository));

        else {

            $filter = $this->requestDataExtractor->searchFilterData($request);
            $ordinationData = $this->requestDataExtractor->searchOrdinationData($request);
            [$currentPage, $itemsPerPage] = $this->requestDataExtractor->searchPaginationData($request);

            $list = $this->repository->findBy(
                $filter,
                $ordinationData,
                $itemsPerPage,
                ($currentPage - 1) * $itemsPerPage
            );

            return new JsonResponse($list);
        }
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

        $fileName = $this->fileHandler->uploadFile($file, '/recipes');

        $recipe->setImage($fileName);

        $this->manager->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    public function getPicture($id): Response
    {
        $recipe = $this->repository->find($id);

        $fileName = $recipe->getImage();

        $fileResponse = $this->fileHandler->downloadFile($fileName, '/recipes');

        return $fileResponse;
    }

    public function rateRecipe(Request $request, $id): JsonResponse
    {
        $recipe = $this->repository->find($id);

        $rate = $this->rateFactory->createRate($request)->setRecipe($recipe);
        $recipe->addRate($rate);

        $this->manager->persist($rate);
        $this->manager->flush();

        return new JsonResponse("", Response::HTTP_OK);
    }

    public function updateRate(Request $request, $id): JsonResponse
    {

    }


}
