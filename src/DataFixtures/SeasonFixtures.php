<?php

namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [ProgramFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_US');

        for ($i = 0; $i < 101; $i++) {
            $season = new Season();
            $season
                ->setNumber($i)
                ->setYear($faker->year($max='now'))
                ->setDescription($faker->realText($maxNbChars = 200, $indexSize = 2));
            $manager->persist($season);

            $this->addReference('season_' . $i, $season);
            $season->setProgram(
                $this->getReference('program_'.random_int(0,30))
            );

            $manager->persist($season);
        }

        $manager->flush();
    }
}
