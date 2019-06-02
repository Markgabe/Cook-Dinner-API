<?php

namespace App\Helper;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Security\JwtAuthenticator;

class UserFactory {

    public function newUser(Request $request) {
        $jsonData= json_decode($request->getContent());

        $user = new User();
        $user
            ->setEmail($jsonData->username)
            ->setPassword($jsonData->password);
        return $user;
    }

    public function listSerialize($list)
    {
            $newArray = array();
            foreach ( $list as $item ) {
                array_push($newArray, $item);
            }

            return $newArray;
    }

    public function getUserByToken(Request $request, $repository)
    {
        $cred = JwtAuthenticator::getCredentials($request);
        $user = $repository->findOneBy([
            'id' => $cred->id
        ]);
        return $user;
    }

}
