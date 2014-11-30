<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\KnockoutMatch;

class KnockoutMatchLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 9;
    }

    public function load(ObjectManager $manager)
    {
        $repository = $manager->getRepository('InsaLanTournamentBundle:KnockoutMatch');
        $rootA = $repository->generateMatches($this->getReference('knockout-1'), 5);
        $rootB = $repository->generateMatches($this->getReference('knockout-2'), 8);
        $manager->flush();

        $lvl1 = $rootA->getChildren()->toArray();
        $lvl21 = $lvl1[0]->getChildren()->toArray();
        $lvl22 = $lvl1[1]->getChildren()->toArray();

        $lvl21[0]->setMatch($this->getReference('match-2'));
        $lvl21[1]->setMatch($this->getReference('match-3'));
        $lvl22[0]->setMatch($this->getReference('match-4'));

        $manager->persist($lvl21[0]);
        $manager->persist($lvl21[1]);
        $manager->persist($lvl22[0]);
        $manager->flush();

        $repository->propagateVictory($lvl21[1]);
        $repository->propagateVictory($lvl21[0]);

        $repository->propagateVictory($lvl22[0], true);

        $repository->propagateVictory($lvl1[1], true); //propagate a direct match
    }
}
