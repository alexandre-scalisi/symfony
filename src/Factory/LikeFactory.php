<?php

namespace App\Factory;

use App\Entity\Like;
use App\Repository\LikeRepository;
use Zenstruck\Foundry\RepositoryProxy;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;

/**
 * @extends ModelFactory<Like>
 *
 * @method static Like|Proxy createOne(array $attributes = [])
 * @method static Like[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Like|Proxy find(object|array|mixed $criteria)
 * @method static Like|Proxy findOrCreate(array $attributes)
 * @method static Like|Proxy first(string $sortedField = 'id')
 * @method static Like|Proxy last(string $sortedField = 'id')
 * @method static Like|Proxy random(array $attributes = [])
 * @method static Like|Proxy randomOrCreate(array $attributes = [])
 * @method static Like[]|Proxy[] all()
 * @method static Like[]|Proxy[] findBy(array $attributes)
 * @method static Like[]|Proxy[] randomSet(int $number, array $attributes = [])
 * @method static Like[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static LikeRepository|RepositoryProxy repository()
 * @method Like|Proxy create(array|callable $attributes = [])
 */
final class LikeFactory extends ModelFactory
{
    public function __construct()
    {
        parent::__construct();

        // TODO inject services if required (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services)
    }

    protected function getDefaults(): array
    {
        return [
            'is_liked' => self::faker()->boolean(60)
        ];
    }

    protected function initialize(): self
    {
        // see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
        return $this
            // ->afterInstantiate(function(Like $like) {})
        ;
    }

    protected static function getClass(): string
    {
        return Like::class;
    }
}