<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Participant;

class ParticipantLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Participant();
        $e->setName('Herpandine');
        $e->setTournament($this->getReference('tournament-1'));
        $manager->persist($e);
        $this->addReference('participant-1', $e);

        $e = new Participant();
        $e->setName('Séssette');
        $e->setTournament($this->getReference('tournament-1'));
        $manager->persist($e);
        $this->addReference('participant-2', $e);

        for ($i = 3; $i <= 8; ++$i) {
            $e = new Participant();
            $e->setName('Part '.$i);
            $e->setTournament($this->getReference('tournament-1'));
            $manager->persist($e);
            $this->addReference('participant-'.$i, $e);
        }

        $manager->flush();
    }
}
