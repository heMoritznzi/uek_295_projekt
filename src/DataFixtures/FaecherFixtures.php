<?php

namespace App\DataFixtures;

use App\Entity\Faecher;
use App\Entity\Note;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FaecherFixtures extends Fixture
{
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

        $manager->flush();
    }
}
