<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;


    public function __construct(UserPasswordHasherInterface $passwordHasher) {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        $users = [];
        

        for($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $users[] = $user;
            $manager->persist($user);
        }
        
        $admin = new User();
        $admin->setEmail('admin@admin.admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setRoles(['ROLE_ADMIN']);
        $users[] = $admin;
        $manager->persist($admin);
        
        
        for($i = 0; $i < 20; $i++) {
                $article = new Article();
                $article->setCreatedAt(new \DateTime($faker->date()));
                $article->setTitle(implode(' ', $faker->words(rand(1, 6))));
                $article->setContent(implode(' ', $faker->sentences(rand(5, 30))));
                $article->setPhoto('https://picsum.photos/200/300?random='.$i);
                $article->setAuthor($users[rand(0, count($users)-1)]);
                $manager->persist($article);
            }
            
        $manager->flush();
    }
}
