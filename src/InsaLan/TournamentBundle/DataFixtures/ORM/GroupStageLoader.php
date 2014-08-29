<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\GroupStage;

class GroupStageLoader extends AbstractFixture implements OrderedFixtureInterface
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

        $manager->flush();
    }
}
