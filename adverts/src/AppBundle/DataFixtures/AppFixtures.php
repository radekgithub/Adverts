<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use AppBundle\Entity\Advert;
use AppBundle\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
            $objects = Fixtures::load(__DIR__ . '/fixture_advert.yml', $manager);
    }
}