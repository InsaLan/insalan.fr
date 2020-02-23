<?php
namespace InsaLan\StreamBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\StreamBundle\Entity\Stream;

class StreamLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Stream();
        $e->setStreamer('streamer-name-0');
        $e->setStreamLink('https://www.youtube.com/embed/UwAOFfFiyi8');
        $e->setOfficial(true);
        $e->setDisplay(true);
        $e->setTournament($this->getReference('tournament-6'));
        $manager->persist($e);

        $e = new Stream();
        $e->setStreamer('streamer-name-1');
        $e->setStreamLink('https://www.youtube.com/embed/2vh19SLj6Ck');
        $e->setOfficial(false);
        $e->setDisplay(true);
        $e->setTournament($this->getReference('tournament-6'));
        $manager->persist($e);

        $manager->flush();
    }
}
