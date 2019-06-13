<?php

namespace App\Helper;


use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\Step;
use App\Repository\RecipeRepository;
use Symfony\Component\HttpFoundation\Request;

class RecipeFactory {

    public function newRecipe(Request $request): Recipe
    {
        $jsonData= json_decode($request->getContent());

        $recipe = new Recipe();

        $recipe
            ->setName($jsonData->name)
            ->setDescription( property_exists($jsonData, 'description') ? $jsonData->description : $jsonData->name)
            ->setTime( property_exists($jsonData, 'time') ? $jsonData->time : 0)
            ->setPortion( property_exists($jsonData, 'portion') ? $jsonData->portion : null)
            ->setGrade(0)
            ->setCreatedAt();

        return $recipe;
    }

    public function updateRecipe(Request $request, Recipe $recipe): Recipe
    {
        $jsonData = json_decode($request->getContent());

        $recipe
            ->setName(property_exists($jsonData, 'name') ? $jsonData->name : $recipe->getName())
            ->setDescription(property_exists($jsonData, 'description') ? $jsonData->description : $recipe->getDescription())
            ->setTime(property_exists($jsonData, 'time') ? $jsonData->time : $recipe->getTime())
            ->setPortion(property_exists($jsonData, 'portion') ? $jsonData->portion : $recipe->getPortion());

        return $recipe;
    }

    public function addStep(Request $request, Recipe $recipe): Step
    {
        $jsonData = json_decode($request->getContent());

        $step = new Step();

        $step
            ->setDescription($jsonData->description)
            ->setNumber($jsonData->number)
            ->setRecipe($recipe);

        $recipe->addStep($step);

        return $step;

    }

    public function addIngredient(Request $request, Recipe $recipe): Ingredient
    {
        $jsonData = json_decode($request->getContent());

        $ingredient = new Ingredient();

        $ingredient
            ->setName($jsonData->name)
            ->setAmount($jsonData->amount)
            ->setRecipe($recipe);

        $recipe->addIngredient($ingredient);

        return $ingredient;

    }

    public function search(Request $request, RecipeRepository $repository)
    {
        $searchKey = array_key_exists('search', $request->query->all())
            ? $request->query->get("search")
            : null;
        $maxResults = array_key_exists('amount', $request->query->all())
            ? $request->query->get("amount")
            : 10;
        $currentPage = array_key_exists('page', $request->query->all())
            ? $request->query->get("page")
            : 1;

        if (!$searchKey) return null;

        else {
            $qb = $repository->createQueryBuilder('u')
                ->select('u')
                ->where('u.name like :entity')
                ->andWhere('u.description like :entity')
                ->orderBy('u.id')
                ->setMaxResults($maxResults)
                ->setFirstResult(($currentPage - 1) * $maxResults)
                ->setParameter('entity', '%' . strtolower($searchKey) . '%');
            return $qb->getQuery()->getResult();
        }
    }

    public function listSerialize($list)
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