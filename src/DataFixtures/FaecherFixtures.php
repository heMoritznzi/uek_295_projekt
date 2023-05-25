<?php

namespace App\DataFixtures;

use App\Entity\Faecher;
use App\Entity\User;
use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class FaecherFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher){

    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faecher = new Faecher();
        $faecher->setFach("Sport");


        $manager->persist($faecher);


        $note = new Note();
        $note->setNote(5);


        $note->setNoteFach($faecher);


        $manager->persist($note);

        $user = new User();
        $user->setUsername("Test");
        $user->setPassword($this->passwordHasher->hashPassword($user, "test1234"));
        $user->setRoles(["ROLE_USER", "ROLE_ADMIN"]);
        $manager->persist($user);


        $user = new User();
        $user->setUsername("Testuser");
        $user->setPassword($this->passwordHasher->hashPassword($user, "test1234"));
        $user->setRoles(["ROLE_USER"]);
        $manager->persist($user);

        $manager->flush();
    }
}
