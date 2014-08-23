<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Group;

class GroupLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Group();
        $e->setName('Group A');
        $e->setStage($this->getReference('groupstage-1'));
        $manager->persist($e);
        $this->addReference('group-1', $e);

        $e = new Group();
        $e->setName('Group B');
        $e->setStage($this->getReference('groupstage-1'));
        $manager->persist($e);
        $this->addReference('group-2', $e);

        $manager->flush();
    }
}
