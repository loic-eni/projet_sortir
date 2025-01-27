<?php

namespace App\Factory;

use App\Entity\Outing;
use App\Entity\State;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Outing>
 */
final class OutingFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Outing::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {

        // Générer une date de début aléatoire
        $startDate = self::faker()->dateTimeBetween('2023-01-01', '+1 years');
        // Générer une date de clôture un mois avant la date de début
        $registrationMaxDate = (clone $startDate)->modify('-'.self::faker()->numberBetween(10, 60).' day');
        // génération de l'état
        $defaultState = StateFactory::find(['label' => State::STATE_OPENED]);
        $state = self::faker()->optional(0.7, StateFactory::find(['label' => State::STATE_CANCELED]))->passthrough($defaultState);

        return [
            'name' => self::faker()->text(20),
            'startDate' => $startDate,
            'duration' => self::faker()->numberBetween(30, 700),
            'registrationMaxDate' => $registrationMaxDate,
            'maxInscriptions' => self::faker()->numberBetween(2, 50),
            'outingInfo' => self::faker()->text(100),
            'state' => $state,
            'location' => LocationFactory::random(),
            'campus' => CampusFactory::random(),
            'organizer' => UserFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Outing $outing): void {})
        ;
    }
}
