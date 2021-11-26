<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TournamentKnockoutMatch;

class TournamentKnockoutMatchLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 9;
    }

    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('App\Entity\TournamentKnockoutMatch');
        $rootA = $repository->generateMatches($this->getReference('knockout-1'), 5, true);
        $rootB = $repository->generateMatches($this->getReference('knockout-2'), 8);
        $manager->flush();

        $rootA = $rootA->getChildren()->toArray();
        $rootA = $rootA[0];

        $lvl1 = $rootA->getChildren()->toArray();
        $lvl21 = $lvl1[0]->getChildren()->toArray();
        $lvl22 = $lvl1[1]->getChildren()->toArray();

        $lvl21[0]->setMatch($this->getReference('match-2')); // Herpandine vs Sessette
        $lvl21[1]->setMatch($this->getReference('match-3')); // Tanche vs Semi-Tanche
        $lvl22[0]->setMatch($this->getReference('match-4')); // Rémi vs (nobody)
        //$lvl22[1]->setMatch($this->getReference('match-5')); // (nobdoy) vs (nobody)

        // populate matches
        $manager->persist($lvl21[0]);
        $manager->persist($lvl21[1]);
        $manager->persist($lvl22[0]);
        //$manager->persist($lvl22[1]);

        $repository->propagateVictoryAll($this->getReference('knockout-1'));

        $manager->flush();
    }
}
