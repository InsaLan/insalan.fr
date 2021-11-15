<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\PizzaOrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TournamentMatch;
use App\Entity\RoyalMatch;

class TournamentMatchLoader extends AbstractFixture implements OrderedFixtureInterface
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

        // Royal groups

        $e = new RoyalMatch();
        for ($p = 1; $p <= 14; $p++) {
            $e->addParticipant($this->getReference('royal-team-'.$p));
            $this->getReference('royal-elite-1')->addParticipant($this->getReference('royal-team-'.$p));
        }
        $e->setState(Match::STATE_FINISHED);
        $e->setGroup($this->getReference('royal-elite-1'));

        $manager->persist($e);
        $this->addReference('royal-match-1', $e);

        $e = new RoyalMatch();
        for ($p = 15; $p <= 28; $p++) {
            $e->addParticipant($this->getReference('royal-team-'.$p));
            $this->getReference('royal-challenger-1')->addParticipant($this->getReference('royal-team-'.$p));
        }
        $e->setState(Match::STATE_FINISHED);
        $e->setGroup($this->getReference('royal-challenger-1'));

        $manager->persist($e);
        $this->addReference('royal-match-2', $e);

        $e = new RoyalMatch();
        for ($p = 1; $p <= 14; $p++) {
            $e->addParticipant($this->getReference('royal-team-'.($p * 2)));
            $this->getReference('royal-elite-2')->addParticipant($this->getReference('royal-team-'.($p * 2)));
        }
        $e->setState(Match::STATE_ONGOING);
        $e->setGroup($this->getReference('royal-elite-2'));

        $manager->persist($e);
        $this->addReference('royal-match-3', $e);

        $e = new RoyalMatch();
        for ($p = 1; $p <= 14; $p++) {
            $e->addParticipant($this->getReference('royal-team-'.($p * 2 - 1)));
            $this->getReference('royal-challenger-2')->addParticipant($this->getReference('royal-team-'.($p * 2 - 1)));
        }
        $e->setState(Match::STATE_ONGOING);
        $e->setGroup($this->getReference('royal-challenger-2'));

        $manager->persist($e);
        $this->addReference('royal-match-4', $e);

        $e = new RoyalMatch();
        $e->setState(Match::STATE_UPCOMING);
        $e->setGroup($this->getReference('royal-elite-3'));

        $manager->persist($e);
        $this->addReference('royal-match-5', $e);

        $e = new RoyalMatch();
        $e->setState(Match::STATE_UPCOMING);
        $e->setGroup($this->getReference('royal-challenger-3'));

        $manager->persist($e);
        $this->addReference('royal-match-6', $e);

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
