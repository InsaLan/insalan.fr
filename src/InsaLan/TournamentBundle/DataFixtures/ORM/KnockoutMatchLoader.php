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
        $rootA = $repository->generateMatches($this->getReference('knockout-1'), 8);
        $rootB = $repository->generateMatches($this->getReference('knockout-2'), 5);

        $children = $rootA->getChildren()->toArray();
        $children = $children[0];
        $children = $children->getChildren()->toArray();

        $gm = $children[0];
        $gm->setMatch($this->getReference('match-2'));

        $manager->persist($gm);
        $manager->flush();

        $repository->propagateVictory($gm);

    }
}
