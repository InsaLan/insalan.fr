<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\GroupMatch;

class GroupMatchLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 4; ++$i) {
            for ($j = $i + 1; $j <= 4; ++$j) {
                $e = new GroupMatch();
                $e->setGroup($this->getReference('group-1'));
                $e->setMatch($this->getReference('match-'.$i.'-'.$j));
                $manager->persist($e);
                $this->addReference('groupmatch-'.$i.'-'.$j, $e);
            }
        }

        for ($i = 5; $i <= 8; ++$i) {
            for ($j = $i + 1; $j <= 8; ++$j) {
                $e = new GroupMatch();
                $e->setGroup($this->getReference('group-2'));
                $e->setMatch($this->getReference('match-'.$i.'-'.$j));
                $manager->persist($e);
                $this->addReference('groupmatch-'.$i.'-'.$j, $e);
            }
        }

        $manager->flush();
    }
}
