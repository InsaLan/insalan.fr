<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Match;

class MatchLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 6;
    }

    public function load(ObjectManager $manager)
    {

        // Group

        $e = new Match();
        $e->setPart1($this->getReference('participant-1'));
        $e->setPart2($this->getReference('participant-2'));
        $e->setState(Match::STATE_FINISHED);
        $e->setGroup($this->getReference('group-1'));

        $manager->persist($e);
        $this->addReference('match-1', $e);

        // Knockout
         
        $e = new Match();
        $e->setPart1($this->getReference('participant-1'));
        $e->setPart2($this->getReference('participant-2'));
        $e->setState(Match::STATE_FINISHED);

        $manager->persist($e);
        $this->addReference('match-2', $e);

        $e = new Match();
        $e->setPart1($this->getReference('participant--1'));
        $e->setPart2($this->getReference('participant--2'));
        $e->setState(Match::STATE_FINISHED);

        $manager->persist($e);
        $this->addReference('match-3', $e);

        $e = new Match();
        $e->setPart1($this->getReference('participant---1'));

        $manager->persist($e);
        $this->addReference('match-4', $e);

        $e = new Match();
        $e->setState(Match::STATE_FINISHED);

        $manager->persist($e);
        $this->addReference('match-5', $e);

        $manager->flush();
    }
}
