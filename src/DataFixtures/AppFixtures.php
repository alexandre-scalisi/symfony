<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
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
        $this->manager = $manager;
        $this->faker = Faker\Factory::create('fr_FR');

        $this->posters = [];
        $this->users = [];

        $this->generateRandomUsers(60);
        
        $this->generateAdmin();

        // les likes et commentaires sont aussi générés indirectement dans cette fonction
        $this->generateRandomArticles(20);
            
        $this->manager->flush();
    }



    private function generateRandomUsers(int $amount) {
        $roles = ['USER', 'POSTER', 'ADMIN'];

        for($i = 0; $i < $amount; $i++) {
            $user = new User();
            $user
                ->setEmail($this->faker->email)
                ->setPassword($this->passwordHasher->hashPassword($user, 'password'))
                ->setUsername($this->faker->userName);

            $role = ['ROLE_' . $roles[rand(0, count($roles) - 1)]];
            $user->setRoles($role);

            if($role !== 'ROLE_USER');
                $this->posters[] = $user;
            $this->users[] = $user;
            $this->manager->persist($user);
        }
    }

    private function generateAdmin() {
        $admin = new User();
        $admin->setEmail('admin@admin.admin')
            ->setUsername('admin')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'password'))
            ->setRoles(['ROLE_ADMIN']);
        $this->posters[] = $admin;
        $this->users[] = $admin;
        $this->manager->persist($admin);
    }

    private function generateRandomArticles(int $amount) {
        for($i = 0; $i < $amount; $i++) {
            $article = new Article();
            $article->setCreatedAt(new \DateTime($this->faker->date()))
                ->setTitle(implode(' ', $this->faker->words(rand(1, 6))))
                ->setContent(implode(' ', $this->faker->sentences(rand(5, 30))))
                ->setPhoto('https://picsum.photos/200/300?random='.$i)
                ->setAuthor($this->posters[rand(0, count($this->posters) - 1)]);
            $this->manager->persist($article);

            // génere un nombre aléatoire de likes aléatoires pour l'article
            $this->generateRandomLikes($article);
            
            // génere un nombre aléatoire de commentaires aléatoires pour l'article
            $this->generateRandomComments($article);
        }
    }

    private function generateRandomLikes($article) {
        // copie de l'array des users dans une nouvelle variable
        $potentialLikers = $this->users;
        for($j = 0; $j < rand(0, count($this->users)); $j++) {
            $like = new Like();
            $like->setArticle($article);
            
            $randomLikerIndex = rand(0, count($potentialLikers) - 1);
            $liker = array_splice($potentialLikers, $randomLikerIndex, 1)[0];
            $like->setLiker($liker)
                 ->setIsLiked(rand(0,1) == 1);

            $this->manager->persist($like);
        }
    }

    private function generateRandomComments($article) {
        $potentialAuthors = $this->users;
        for($j = 0; $j < rand(0, count($this->users)); $j++) {
            $comment = new Comment();
            $comment->setMessage($this->faker->text);
            $comment->setArticle($article);
            $comment->setCreatedAt($this->faker->dateTimeBetween($article->getCreatedAt(), new \DateTime()));
            

            $randomAuthorIndex = rand(0, count($potentialAuthors) - 1);
            $author = array_splice($potentialAuthors, $randomAuthorIndex, 1)[0];
            $comment->setAuthor($author);

            $this->manager->persist($comment);
        }
    }
}