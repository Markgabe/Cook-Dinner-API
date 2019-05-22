<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('teste@teste.com')
             ->setPassword('$argon2i$v=19$m=1024,t=2,p=2$NmNkNDNsTi5MZnJhdWVIMg$15sJ6R51xLrzH0rPJOE2SI4qvzXHED8dyv/4mlO/ldQ');

        $manager->persist($user);
        $manager->flush();
    }
}
