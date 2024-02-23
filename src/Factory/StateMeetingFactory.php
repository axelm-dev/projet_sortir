<?php

namespace App\Factory;

use App\Entity\StateMeeting;
use App\Repository\StateMeetingRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<StateMeeting>
 *
 * @method        StateMeeting|Proxy                     create(array|callable $attributes = [])
 * @method static StateMeeting|Proxy                     createOne(array $attributes = [])
 * @method static StateMeeting|Proxy                     find(object|array|mixed $criteria)
 * @method static StateMeeting|Proxy                     findOrCreate(array $attributes)
 * @method static StateMeeting|Proxy                     first(string $sortedField = 'id')
 * @method static StateMeeting|Proxy                     last(string $sortedField = 'id')
 * @method static StateMeeting|Proxy                     random(array $attributes = [])
 * @method static StateMeeting|Proxy                     randomOrCreate(array $attributes = [])
 * @method static StateMeetingRepository|RepositoryProxy repository()
 * @method static StateMeeting[]|Proxy[]                 all()
 * @method static StateMeeting[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static StateMeeting[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static StateMeeting[]|Proxy[]                 findBy(array $attributes)
 * @method static StateMeeting[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static StateMeeting[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class StateMeetingFactory extends ModelFactory
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
            'value' => self::faker()->text(50),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(StateMeeting $stateMeeting): void {})
        ;
    }

    protected static function getClass(): string
    {
        return StateMeeting::class;
    }
}
