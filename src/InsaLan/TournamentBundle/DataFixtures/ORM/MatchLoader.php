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
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 4; ++$i) {
            for ($j = $i + 1; $j <= 4; ++$j) {
                $e = new Match();
                $e->setPart1($this->getReference('participant-'.$i));
                $e->setPart2($this->getReference('participant-'.$j));
                $manager->persist($e);
                $this->addReference('match-'.$i.'-'.$j, $e);
            }
        }

        for ($i = 5; $i <= 8; ++$i) {
            for ($j = $i + 1; $j <= 8; ++$j) {
                $e = new Match();
                $e->setPart1($this->getReference('participant-'.$i));
                $e->setPart2($this->getReference('participant-'.$j));
                $manager->persist($e);
                $this->addReference('match-'.$i.'-'.$j, $e);
            }
        }

        $manager->flush();
    }
}
