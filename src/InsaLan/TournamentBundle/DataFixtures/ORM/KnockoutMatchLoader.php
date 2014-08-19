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
        return 8;
    }

    public function load(ObjectManager $manager)
    {
        $e = new KnockoutMatch();
        $e->setKnockout($this->getReference('knockout-1'));
        $manager->persist($e);
        $this->addReference('knockoutmatch-2-1', $e);

        $e = new KnockoutMatch();
        $e->setKnockout($this->getReference('knockout-1'));
        $e->setParent($this->getReference('knockoutmatch-2-1'));
        $manager->persist($e);
        $this->addReference('knockoutmatch-1-1', $e);

        $e = new KnockoutMatch();
        $e->setKnockout($this->getReference('knockout-1'));
        $e->setParent($this->getReference('knockoutmatch-2-1'));
        $manager->persist($e);
        $this->addReference('knockoutmatch-1-2', $e);

        $manager->flush();
    }
}
