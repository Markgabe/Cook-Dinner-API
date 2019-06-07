<?php

namespace App\Helper;


use App\Entity\Recipe;
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