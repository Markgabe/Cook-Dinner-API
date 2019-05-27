<?php

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class UserFactory {

    public function newUser(Request $request) {
        $jsonRequest = json_decode($request->getContent());

        $user = new User();
        $user
            ->setEmail($jsonRequest->email)
            ->setPassword($jsonRequest->senha);
        return $user;
    }

    public function listSerialize($lis)
    {
            $newArray = array();
            $list = json_decode($lis);
            foreach ( $list as $item ) {
                $new = (object) ['id' => $item->id, 'email' => $item->email];
                array_push($newArray, $new);
            }

            return $newArray;
    }

    public function getUserByToken(Request $request, $repository)
    {
        $cred = JwtAutenticador::getCredentials($request);
        $user = $repository->findOneBy([
            'email' => $cred->email
        ]);
        return $user;
    }

}