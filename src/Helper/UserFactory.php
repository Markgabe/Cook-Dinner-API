<?php

namespace App\Helper;

use App\Entity\User;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use App\Security\JwtAuthenticator;

class UserFactory {

    public function newUser(Request $request) {
        $jsonData= json_decode($request->getContent());

        $user = new User();
        $user
            ->setEmail($jsonData->username)
            ->setPassword($jsonData->password)
            ->setName( property_exists($jsonData, 'name') ? $jsonData->name : "User")
            ->setGender( property_exists($jsonData, 'gender') ? $jsonData->gender : "Undefined")
            ->setBirthday( property_exists($jsonData, 'birthday') ? new DateTime($jsonData->birthday) : new DateTime("00/00/00"))
            ->setCreatedAt();
        return $user;
    }

    public function updateUser(Request $request, User $user): User
    {
        $jsonData = json_decode($request->getContent());

        $user
            ->setName( property_exists($jsonData, 'name') ? $jsonData->name : $user->getName())
            ->setGender( property_exists($jsonData, 'gender') ? $jsonData->gender : $user->getGender())
            ->setBirthday( property_exists($jsonData, 'birthday') ? new DateTime($jsonData->birthday) : $user->getBirthday());

        return $user;
    }

    public function setPassword(Request $request, User $user): User
    {
        $jsonData = json_decode($request->getContent());

        $user->setPassword($jsonData->new_password);

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
