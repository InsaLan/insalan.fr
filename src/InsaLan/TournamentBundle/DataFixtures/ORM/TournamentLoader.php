<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Tournament;

class TournamentLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Tournament();
        $e->setName('CS: GO');
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(64);
        $e->setTeamMaxPlayer(8);
        $e->setTeamMinPlayer(5);
        $e->setType('manual');
        $e->setLogoPath('fixtures-1.png');
        $e->setParticipantType('team');
        $manager->persist($e);
        $this->addReference('tournament-1', $e);

        $e = new Tournament();
        $e->setName('League of Legends');
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(64);
        $e->setTeamMaxPlayer(8);
        $e->setTeamMinPlayer(5);
        $e->setType('lol');
        $e->setLogoPath('fixtures-2.png');
        $e->setParticipantType('team');
        $manager->persist($e);
        $this->addReference('tournament-2', $e);

        $e = new Tournament();
        $e->setName('StarCraft II');
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(1);
        $e->setTeamMaxPlayer(1);
        $e->setTeamMinPlayer(1);
        $e->setType('manual');
        $e->setLogoPath('fixtures-3.png');
        $e->setParticipantType('player');
        $manager->persist($e);
        $this->addReference('tournament-3', $e);

        $e = new Tournament();
        $e->setName('TF2');
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(1);
        $e->setTeamMaxPlayer(1);
        $e->setTeamMinPlayer(1);
        $e->setType('manual');
        $e->setLogoPath('fixtures-4.png');
        $e->setParticipantType('team');
        $manager->persist($e);
        $this->addReference('tournament-4', $e);

        $manager->flush();
    }
}
