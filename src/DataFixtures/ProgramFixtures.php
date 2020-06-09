<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;
use App\Service\Slugify;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    const PROGRAMS = [
        'Community' => [
            'summary' => 'A suspended lawyer is forced to enroll in a community college with an eccentric staff and student body.',
            'poster' =>'https://m.media-amazon.com/images/M/MV5BNDQ5NDZiYjktZmFmMy00MjAxLTk1MDktOGZjYTY5YTE1ODdmXkEyXkFqcGdeQXVyNjcwMzEzMTU@._V1_UY268_CR2,0,182,268_AL_.jpg',
            'category' => 'category_0',
        ],
        'Brooklyn Nine-Nine' => [
            'summary' => '"Brooklyn Nine-Nine" follows the exploits of hilarious Det. Jake Peralta and his diverse, lovable colleagues as they police the NYPD\'s 99th Precinct.',
            'poster' =>'https://m.media-amazon.com/images/M/MV5BNzVkYWY4NzYtMWFlZi00YzkwLThhZDItZjcxYTU4ZTMzMDZmXkEyXkFqcGdeQXVyODUxOTU0OTg@._V1_UX182_CR0,0,182,268_AL_.jpg',
            'category' => 'category_0',
        ],
        'Fleabag' => [
            'summary' => 'A comedy series adapted from the award-winning play about a young woman trying to cope with life in London whilst coming to terms with a recent tragedy.',
            'poster' =>'https://m.media-amazon.com/images/M/MV5BMjA4MzU5NzQxNV5BMl5BanBnXkFtZTgwOTg3MDA5NzM@._V1_UX182_CR0,0,182,268_AL_.jpg',
            'category' => 'category_0',
        ],
        'Penny Dreadful' => [
            'summary' => 'Explorer Sir Malcolm Murray, American gunslinger Ethan Chandler, scientist Victor Frankenstein and medium Vanessa Ives unite to combat supernatural threats in Victorian London.',
            'poster' => 'https://m.media-amazon.com/images/M/MV5BMTQ0Mzg2NzcyNl5BMl5BanBnXkFtZTgwMDE1NzU2NDE@._V1_UY268_CR7,0,182,268_AL_.jpg',
            'category' => 'category_2',
        ],
        'The Mandalorian' => [
            'summary' => 'The travels of a lone bounty hunter in the outer reaches of the galaxy, far from the authority of the New Republic.',
            'poster' => 'https://m.media-amazon.com/images/M/MV5BMWI0OTJlYTItNzMwZi00YzRiLWJhMjItMWRlMDNhZjNiMzJkXkEyXkFqcGdeQXVyMTkxNjUyNQ@@._V1_UX182_CR0,0,182,268_AL_.jpg',
            'category' => 'category_3',
        ],
        'GLOW' => [
            'summary' => 'A look at the personal and professional lives of a group of women who perform for a wrestling organization in Los Angeles during the 1980s.',
            'poster' => 'https://m.media-amazon.com/images/M/MV5BY2RjYzFkZDUtYzNjNC00MzEyLWFmZmItODc2YWFlOWExOWI4XkEyXkFqcGdeQXVyNDg4NjY5OTQ@._V1_UX182_CR0,0,182,268_AL_.jpg',
            'category' => 'category_0',
        ],
    ];

    public function getDependencies()
    {
        return [CategoryFixtures::class];
    }

    public function load(ObjectManager $manager)
    {
        $i = 0;
        foreach (self::PROGRAMS as $title => $data) {
            $program = new Program();
            $program->setTitle($title);
            $program->setSummary($data['summary']);
            $program->setPoster($data['poster']);

            $slugify = new Slugify();
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);

            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
            $program->setCategory(
                $this->getReference($data['category'])
            );
            $i++;
        }

        $faker = Faker\Factory::create('en_US');

        for ($i = 6; $i < 31; $i++) {
            $program = new Program();
            $program
                ->setTitle($faker->company())
                ->setSummary($faker->realText($maxNbChars = 200, $indexSize = 2))
                ->setPoster($faker->imageUrl(80, 120));
            $slug = $slugify->generate($program->getTitle());
            $program->setSlug($slug);
            $manager->persist($program);
            $this->addReference('program_' . $i, $program);
            $program->setCategory(
                $this->getReference('category_'.random_int(0,10))
            );

            $manager->persist($program);
        }

        $manager->flush();
    }
}
