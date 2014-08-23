<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Round;

class RoundLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 7;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 4; ++$i) {
            for ($j = $i + 1; $j <= 4; ++$j) {
                for ($k = 0; $k < 2; ++$k) {
                    $e = new Round();
                    $e->setMatch($this->getReference('match-'.$i.'-'.$j));
                    $e->setScore1(mt_rand(0, 1));
                    $e->setScore2(1 - $e->getScore1());
                    $manager->persist($e);
                }
            }
        }

        for ($i = 5; $i <= 8; ++$i) {
            for ($j = $i + 1; $j <= 8; ++$j) {
                for ($k = 0; $k < 2; ++$k) {
                    $e = new Round();
                    $e->setMatch($this->getReference('match-'.$i.'-'.$j));
                    $e->setScore1(mt_rand(0, 1));
                    $e->setScore2(1 - $e->getScore1());
                    $manager->persist($e);
                }
            }
        }

        $manager->flush();
    }
}
