<?php

namespace App\DataFixtures;

use App\Entity\State;
use App\Factory\CampusFactory;
use App\Factory\CityFactory;
use App\Factory\LocationFactory;
use App\Factory\OutingFactory;
use App\Factory\StateFactory;
use App\Factory\UserFactory;
use App\Service\OutingService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{

    public function __construct(private readonly OutingService $outingService)
    {
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function load(ObjectManager $manager): void
    {
        CityFactory::createMany(10);
        CampusFactory::createMany(10);
        LocationFactory::createMany(10);

        UserFactory::createOne([
            'email'=> 'admin@admin.fr',
            'firstname' => 'admin',
            'lastname' => 'admin',
            'password' => 'admin',
            'roles' => ['ROLE_ADMIN'],
            'admin' => true
        ]);
        UserFactory::createOne([
            'email'=> 'user@user.fr',
            'firstname' => 'user',
            'lastname' => 'user',
            'password' => 'user',
            'roles' => ['ROLE_USER'],
            'admin' => false
        ]);

        StateFactory::createOne(['label' => State::STATE_CREATED]);
        StateFactory::createOne(['label' => State::STATE_OPENED]);
        StateFactory::createOne(['label' => State::STATE_CLOSED]);
        StateFactory::createOne(['label' => State::STATE_ACTIVITY_IN_PROGRESS]);
        StateFactory::createOne(['label' => State::STATE_PASSED]);
        StateFactory::createOne(['label' => State::STATE_CANCELED]);
        StateFactory::createOne(['label' => State::STATE_ARCHIVED]);

        $faker = Factory::create();
        $date = $faker->dateTimeBetween('-3 months', '-2 months');
        OutingFactory::createOne([
            'startDate' => $date,
            'registrationMaxDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 50,
        ]);
        $date = new \DateTime();
        OutingFactory::createOne([
            'startDate' => $date,
            'registrationMaxDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 9999,
        ]);
        $faker = Factory::create();
        $date = $faker->dateTimeBetween('-1month', '-1day');
        OutingFactory::createOne([
            'startDate' => $date,
            'registrationMaxDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 1,
        ]);
        OutingFactory::createOne([
            'startDate' => $date,
            'registrationMaxDate' => (clone $date)->modify('-2 weeks'),
            'duration' => 60,
            'state' => StateFactory::find(['label' => STATE::STATE_CANCELED]),
        ]);

        OutingFactory::createMany(20);


        UserFactory::createMany(20, function() {
            return [
                'Outings' => OutingFactory::randomRange(0, 5),
            ];
        });

        $this->outingService->autoUpdateOutingStates();
    }
}
