<?php

namespace App\Factory;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\ArticleRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Article>
 *
 * @method static Article|Proxy createOne(array $attributes = [])
 * @method static Article[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Article|Proxy find(object|array|mixed $criteria)
 * @method static Article|Proxy findOrCreate(array $attributes)
 * @method static Article|Proxy first(string $sortedField = 'id')
 * @method static Article|Proxy last(string $sortedField = 'id')
 * @method static Article|Proxy random(array $attributes = [])
 * @method static Article|Proxy randomOrCreate(array $attributes = [])
 * @method static Article[]|Proxy[] all()
 * @method static Article[]|Proxy[] findBy(array $attributes)
 * @method static Article[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Article[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static ArticleRepository|RepositoryProxy repository()
 * @method Article|Proxy create(array|callable $attributes = [])
 */
final class ArticleFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function getDefaults(): array
    {
        return [
            'title' => implode(' ', self::faker()->words(rand(1, 6))),
            'content' => self::faker()->realText(),
            'photo' => 'https://picsum.photos/200/300?random=' . self::faker()->unique()->numberBetween(0, 100),
            'createdAt' => self::faker()->dateTimeBetween(),
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Article $article) {
            // })
        ;
    }

    protected static function getClass(): string
    {
        return Article::class;
    }
}