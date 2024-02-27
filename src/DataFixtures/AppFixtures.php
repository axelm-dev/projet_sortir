<?php

namespace App\DataFixtures;

use App\Controller\ProjectController;
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
        CityFactory::createMany(10);

        $cities = CityFactory::randomSet(5);
        CampusFactory::createMany(5, fn() => ['name' => 'ENI ' . $cities[CityFactory::faker()->unique()->numberBetween(0, 4)]->getName()]);

        UserFactory::createMany(10);
        $user = UserFactory::createOne([
            'email' => 'user@test.local',
            'login' => 'user',
            'roles' => ['ROLE_USER'],
            'profile' => ProfileFactory::createOne(
                [
                    'firstName' => 'user',
                    'lastName' => 'USER',
                    'phone' => ProfileFactory::faker()->e164PhoneNumber(),
                ]
            )
        ]);
        $admin = UserFactory::createOne([
            'email' => 'admin@test.local',
            'login' => 'admin',
            'roles' => ['ROLE_ADMIN']
        ]);
        UserFactory::createMany(20);

        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_CREATED]);
        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_OPENED]);
        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_CLOSED]);
        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_ACTIVITY]);
        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_PASSED]);
        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_CANCELED]);
        StateMeetingFactory::createOne(['value' => ProjectController::STATE_MEETING_ARCHIVED]);

        PlaceFactory::createMany(10);

        MeetingFactory::createMany(50);

        // sortie archivée
        $date = MeetingFactory::faker()->dateTimeBetween('-3month', '-2month');
        MeetingFactory::createOne([
            'name' => 'sortie archivée',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 50,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_OPENED]),
        ]);
        // sortie en cours
        $date = new \DateTime();
        MeetingFactory::createOne([
            'name' => 'sortie en cours',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 9999,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_OPENED]),
        ]);
        // sortie passé
        $date = MeetingFactory::faker()->dateTimeBetween('-1month', '-1day');
        MeetingFactory::createOne([
            'name' => 'sortie passée',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 1,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_OPENED]),
        ]);
        // sortie annulée
        $date = new \DateTime();
        MeetingFactory::createOne([
            'name' => 'sortie annulée',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 60,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_CANCELED]),
        ]);
        // sortie ouverte
        $date = MeetingFactory::faker()->dateTimeBetween('+2week', '+2month');
        MeetingFactory::createOne([
            'name' => 'sortie ouverte',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 120,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_OPENED]),
        ]);

        // sortie créée dont je suis l'organisateur
        $date = MeetingFactory::faker()->dateTimeBetween('+2week', '+2month');
        MeetingFactory::createOne([
            'name' => 'je suis organisateur Créée',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-1 weeks'),
            'duration' => 120,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_CREATED]),
            'organizer' => $user
        ]);

        // sortie ouverte dont je suis l'organisateur
        $date = MeetingFactory::faker()->dateTimeBetween('+2week', '+2month');
        MeetingFactory::createOne([
            'name' => 'je suis organisateur publié',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-1 weeks'),
            'duration' => 120,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_OPENED]),
            'organizer' => $user
        ]);

        // Sortie qui arrive soon et inscription rempli
        $date = MeetingFactory::faker()->dateTimeBetween('+2week', '+2month');
        MeetingFactory::createOne([
            'name' => 'Rempli à craqué',
            'date' => $date,
            'limitDate' => (clone $date)->modify('-1 weeks'),
            'duration' => 60,
            'state' => StateMeetingFactory::find(['value' => ProjectController::STATE_MEETING_OPENED]),
            'usersMax' => 2,
            'participants' => UserFactory::randomRange(2,2)
        ]);
        
    }
}
