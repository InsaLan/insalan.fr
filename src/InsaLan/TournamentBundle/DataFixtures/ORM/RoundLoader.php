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
        
        for($i = 1; $i < 4; $i++)
        {
            $e = new Round();
            $e->setMatch($this->getReference('match-'.$i));
            
            $manager->persist($e);
            $manager->flush();

            $e->setScore($e->getMatch()->getPart1(), mt_rand(0, 1));
            $e->setScore($e->getMatch()->getPart2(), 1 - $e->getScore($e->getMatch()->getPart1()));
            $manager->persist($e);
        }

        for($i = 1; $i <= 4; $i++)
        {
            for ($r = 1; $r <= 4; $r++)
            {
                $e = new Round();
                $e->setMatch($this->getReference('royal-match-'.$i));
                
                $manager->persist($e);
                $manager->flush();

                $sc = 0;
                foreach ($e->getMatch()->getParticipants() as $p) {
                    $e->setScore($p, ($sc++ + $r) % 14);
                }

                $manager->persist($e);
            }
        }

        $manager->flush();
    }
}
