<?php

namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\Service\Slugify;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies()
    {
        return [SeasonFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('en_US');

        for ($i = 0; $i < 501; $i++) {
            $episode = new Episode();
            $episode
                ->setTitle($faker->company())
                ->setNumber($i)
                ->setSynopsis($faker->realText($maxNbChars = 200, $indexSize = 2));

            $slugify = new Slugify();
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);

            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
            $episode->setSeason(
                $this->getReference('season_'.random_int(0,100))
            );

            $manager->persist($episode);
        }

        $manager->flush();
    }
}
