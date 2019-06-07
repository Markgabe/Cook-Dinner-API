<?php

namespace App\Helper;

use App\Entity\Rate;
use Symfony\Component\HttpFoundation\Request;

class RateFactory {

    public function newRate(Request $request): Rate
    {
        $jsonData= json_decode($request->getContent());

        $rate = new Rate();

        $rate
            ->setFavorite( property_exists($jsonData, 'favorite') ? $jsonData->favorite : false)
            ->setGrade(property_exists($jsonData, 'grade') ? $jsonData->grade : null)
            ->setCreatedAt();

        return $rate;
    }

    public function updateRate(Request $request): Rate
    {
        return new Rate();
    }

}