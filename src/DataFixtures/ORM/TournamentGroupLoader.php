<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TournamentGroup;

class TournamentGroupLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager)
    {
        $e = new TournamentGroup();
        $e->setName('Group A');
        $e->setStage($this->getReference('groupstage-1'));
        $e->addParticipant($this->getReference('participant-1'));
        $e->addParticipant($this->getReference('participant-2'));
        $manager->persist($e);
        $this->addReference('group-1', $e);

        $e = new TournamentGroup();
        $e->setName('Group B');
        $e->setStage($this->getReference('groupstage-1'));
        $manager->persist($e);
        $this->addReference('group-2', $e);

        for ($i = 1; $i <= 5; $i++) {
            $e = new TournamentGroup();
            $e->setName('Elite '.$i);
            $e->setStage($this->getReference('royal-stage-'.$i));
            $e->setStatsType(TournamentGroup::STATS_SCORE);
            $manager->persist($e);
            $this->addReference('royal-elite-'.$i, $e);

            $e = new TournamentGroup();
            $e->setName('Challenger '.$i);
            $e->setStage($this->getReference('royal-stage-'.$i));
            $e->setStatsType(TournamentGroup::STATS_SCORE);
            $manager->persist($e);
            $this->addReference('royal-challenger-'.$i, $e);
        }

        $manager->flush();
    }
}
