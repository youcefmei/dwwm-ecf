<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 15; ++$i) {
            $user = new User();
            $user->setEmail('user_'. $i.'@gmail.com');
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'azertyuiop0'));
            $manager->persist($user);
        }

        $manager->flush();
    }
}