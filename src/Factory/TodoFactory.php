<?php

namespace App\Factory;

use App\Entity\Todo;
use App\Repository\TodoRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Todo>
 *
 * @method        Todo|Proxy create(array|callable $attributes = [])
 * @method static Todo|Proxy createOne(array $attributes = [])
 * @method static Todo|Proxy find(object|array|mixed $criteria)
 * @method static Todo|Proxy findOrCreate(array $attributes)
 * @method static Todo|Proxy first(string $sortedField = 'id')
 * @method static Todo|Proxy last(string $sortedField = 'id')
 * @method static Todo|Proxy random(array $attributes = [])
 * @method static Todo|Proxy randomOrCreate(array $attributes = [])
 * @method static TodoRepository|RepositoryProxy repository()
 * @method static Todo[]|Proxy[] all()
 * @method static Todo[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Todo[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Todo[]|Proxy[] findBy(array $attributes)
 * @method static Todo[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Todo[]|Proxy[] randomSet(int $number, array $attributes = [])
 */
final class TodoFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'title' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Todo $todo): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Todo::class;
    }
}
