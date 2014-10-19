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
        $manager->persist($e);
        $this->addReference('tournament-1', $e);

        $e = new Tournament();
        $e->setName('League of Legends');
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(64);
        $manager->persist($e);
        $this->addReference('tournament-2', $e);

        $manager->flush();
    }
}
