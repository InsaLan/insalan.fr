<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\PizzaOrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TournamentGroupStage;

class TournamentGroupStageLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $e = new GroupStage();
        $e->setName('Stage 1');
        $e->setTournament($this->getReference('tournament-1'));
        $manager->persist($e);
        $this->addReference('groupstage-1', $e);

        $e = new GroupStage();
        $e->setName('Stage 2');
        $e->setTournament($this->getReference('tournament-1'));
        $manager->persist($e);
        $this->addReference('groupstage-2', $e);

        for ($i = 1; $i <= 5; $i++) {
            $e = new GroupStage();
            $e->setName('Tour ' . $i);
            $e->setTournament($this->getReference('tournament-5'));
            $manager->persist($e);
            $this->addReference('royal-stage-'.$i, $e);
        }

        $manager->flush();
    }
}
