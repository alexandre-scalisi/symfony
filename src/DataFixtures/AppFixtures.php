<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');


        for($i = 0; $i < 10; $i++) {
            $user = new User;
            $user->setEmail($faker->email);
            $user->setPassword('password');

            $manager->persist($user);
        }


        for($i = 0; $i < 20; $i++) {
            $article = new Article();
            $article->setCreatedAt(new \DateTime($faker->date()));
            $article->setTitle(implode(' ', $faker->words(rand(1, 6))));
            $article->setContent(implode(' ', $faker->sentences(rand(5, 30))));
            $article->setPhoto('https://picsum.photos/200/300?random='.$i);
            $article->setAuthor();
            $manager->persist($article);
        }
        
        $manager->flush();
    }
}
