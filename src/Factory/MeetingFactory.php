<?php

namespace App\Factory;

use App\Entity\Meeting;
use App\Repository\MeetingRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Meeting>
 *
 * @method        Meeting|Proxy                     create(array|callable $attributes = [])
 * @method static Meeting|Proxy                     createOne(array $attributes = [])
 * @method static Meeting|Proxy                     find(object|array|mixed $criteria)
 * @method static Meeting|Proxy                     findOrCreate(array $attributes)
 * @method static Meeting|Proxy                     first(string $sortedField = 'id')
 * @method static Meeting|Proxy                     last(string $sortedField = 'id')
 * @method static Meeting|Proxy                     random(array $attributes = [])
 * @method static Meeting|Proxy                     randomOrCreate(array $attributes = [])
 * @method static MeetingRepository|RepositoryProxy repository()
 * @method static Meeting[]|Proxy[]                 all()
 * @method static Meeting[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Meeting[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Meeting[]|Proxy[]                 findBy(array $attributes)
 * @method static Meeting[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Meeting[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class MeetingFactory extends ModelFactory
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
        $limitDate = self::faker()->dateTimeBetween('-4month', '+6month');
        $usersMax = self::faker()->randomNumber(2);
        return [
            'date' => self::faker()->dateTimeBetween($limitDate, '+8month'),
            'duration' => self::faker()->randomNumber(3),
            'limitDate' => $limitDate,
            'name' => self::faker()->words(2, true),
            'usersMax' => $usersMax,
            'organizer' => UserFactory::random(),
            'state' => StateMeetingFactory::random(),
            'place' => PlaceFactory::random(),
            'campus' => CampusFactory::random(),
            'participants' => UserFactory::randomRange(0,$usersMax > UserFactory::count() ? UserFactory::count() : $usersMax)
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Meeting $meeting): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Meeting::class;
    }
}
