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
            ->setDescription($jsonData->description)
            ->setCreatedAt()
            ->setTime(0);

        return $recipe;
    }

    public function updateRecipe(Request $request, Recipe $recipe): Recipe
    {
        $jsonData = json_decode($request->getContent());

        $recipe
            ->setName($jsonData->name)
            ->setDescription($jsonData->description)
            ->setTime($jsonData->time)
            ->setPortion($jsonData->portion);

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