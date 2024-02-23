<?php

namespace App\DataFixtures;

use App\Factory\CampusFactory;
use App\Factory\CityFactory;
use App\Factory\MeetingFactory;
use App\Factory\PlaceFactory;
use App\Factory\ProfileFactory;
use App\Factory\StateMeetingFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        UserFactory::createOne(['roles' => ['ROLE_ADMIN']]);

        ProfileFactory::createMany(10);

        CityFactory::createMany(10);


        $cities = CityFactory::randomSet(5);
        CampusFactory::createMany(5, fn() => ['name' => $cities[CityFactory::faker()->unique()->numberBetween(0, 4)]->getName()]);


        PlaceFactory::createMany(10);

        StateMeetingFactory::createOne(['value' => 'Créée']);
        StateMeetingFactory::createOne(['value' => 'Ouverte']);
        StateMeetingFactory::createOne(['value' => 'Clôturée']);
        StateMeetingFactory::createOne(['value' => "Activité en cours"]);
        StateMeetingFactory::createOne(['value' => 'Passée']);
        StateMeetingFactory::createOne(['value' => 'Annulée']);

        MeetingFactory::createMany(50);

        $manager->flush();
    }
}
