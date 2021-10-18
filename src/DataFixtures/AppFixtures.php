<?php

namespace App\DataFixtures;

use App\Factory\ArticleFactory;
use App\Factory\CommentFactory;
use App\Factory\LikeFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $manager)
    {
        UserFactory::createMany(25);
        UserFactory::createOne(['email' => 'admin@admin.com', 'username' => 'admin']);
        UserFactory::createOne(['email' => 'user@user.com', 'username' => 'user']);

        $articles = ArticleFactory::createMany(50, function () {
            return [
                'author' => UserFactory::random()
            ];
        });

        CommentFactory::createMany(360, function () {
            $randomArticle = ArticleFactory::random();
            return [
                'author' => UserFactory::random(),
                'article' => $randomArticle,
                'createdAt' => Factory::create()->dateTimeBetween($randomArticle->getCreatedAt())
            ];
        });


        foreach ($articles as $article) {
            $randomUsers = UserFactory::randomRange(0, UserFactory::count());

            foreach ($randomUsers as $randomUser) {
                LikeFactory::createOne(['liker' => $randomUser, 'article' => $article]);
            }
        }
    }
}