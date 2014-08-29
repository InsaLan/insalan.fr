<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\GroupParticipant;

class GroupParticipantLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        for ($j = 1; $j <= 2; ++$j) {
            for ($i = 1; $i <= 4; ++$i) {
/*                $e = new GroupParticipant();
                $e->setGroup($this->getReference('group-'.$j));
                $e->setParticipant($this->getReference('participant-'.($j*4 + $i - 4)));
                $manager->persist($e);
                $this->addReference('groupparticipant-'.$i.'-'.($j*4 + $i - 4), $e);
*/

                $group = $this->getReference('group-'.$j);
                $group->addParticipant($this->getReference('participant-'.($j*4 + $i - 4)));
                $manager->persist($group);
            }
        }

        $manager->flush();
    }
}
