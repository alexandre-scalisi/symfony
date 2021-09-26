<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Like;
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

        $posters = [];
        $users = [];
        $roles = ['USER', 'POSTER', 'ADMIN'];
        for($i = 0; $i < 60; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $user->setUsername($faker->userName);
            $role = ['ROLE_' . $roles[rand(0, count($roles) - 1)]];
            $user->setRoles($role);
            if($role !== 'ROLE_USER');
                $posters[] = $user;
            $users[] = $user;
            $manager->persist($user);
        }
        
        $admin = new User();
        $admin->setEmail('admin@admin.admin');
        $admin->setUsername('admin');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setRoles(['ROLE_ADMIN']);
        $posters[] = $admin;
        $users[] = $admin;
        $manager->persist($admin);


        
        for($i = 0; $i < 20; $i++) {
                $article = new Article();
                $article->setCreatedAt(new \DateTime($faker->date()));
                $article->setTitle(implode(' ', $faker->words(rand(1, 6))));
                $article->setContent(implode(' ', $faker->sentences(rand(5, 30))));
                $article->setPhoto('https://picsum.photos/200/300?random='.$i);
                $article->setAuthor($posters[rand(0, count($posters) -1)]);
                $manager->persist($article);

                $likers = $users;
                for($j = 0; $j < rand(0, count($users)); $j++) {
                    $like = new Like();
                    $like->setArticle($article);
                    
                    $randomLikerIndex = rand(0, count($likers) - 1);
                    $liker = array_splice($likers, $randomLikerIndex, 1)[0];
                    $like->setLiker($liker);
                    $like->setIsLiked(rand(0,1) == 1);

                    $manager->persist($like);
                }
            }
            
        $manager->flush();
    }
}
