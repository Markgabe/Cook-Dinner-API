<?php

namespace App\Helper;

use App\Entity\Rate;
use Symfony\Component\HttpFoundation\Request;

class RateFactory {

    public function createRate(Request $request): Rate
    {
        $jsonData = json_decode($request->getContent());

        $rate = new Rate();
        $rate
            ->setGrade($jsonData->grade)
            ->setFavorite($jsonData->favorite)
            ->setCreatedAt();

        return $rate;
    }

    public function updateRate(Request $request): Rate
    {
        return new Rate();
    }

}