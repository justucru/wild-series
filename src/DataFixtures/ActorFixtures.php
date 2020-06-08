<?php

namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;


class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    const ACTORS = [
        'Alison Brie' => [
            'program_0',
            'program_5'
        ],
        'Andy Samberg' => [
            'program_1'
        ],
        'Phoebe Waller-Bridge' => [
            'program_2'
        ],
        'Eva Green' => [
            'program_3'
        ],
    ];

    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach (self::ACTORS as $name => $programs) {
            $actor = new Actor();
            $actor->setName($name);
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            foreach ($programs as $program) {
                $actor->addProgram(
                    $this->getReference($program)
                );
            }
            $i++;
        }

        $faker = Faker\Factory::create('en_US');

        for ($i = 4; $i < 51; $i++) {
            $actor = new Actor();
            $actor->setName($faker->name(null));
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
            $actor->addProgram(
                $this->getReference('program_' . random_int(0, 30)));

        }

        $manager->flush();
    }
}
