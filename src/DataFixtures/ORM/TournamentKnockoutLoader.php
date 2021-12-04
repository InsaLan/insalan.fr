<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\TournamentKnockout;

class TournamentKnockoutLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 8;
    }

    public function load(ObjectManager $manager)
    {
        $e = new TournamentKnockout();
        $e->setName('Elite bracket');
        $e->setTournament($this->getReference('tournament-1'));
        $manager->persist($e);
        $this->addReference('knockout-1', $e);

        $e = new TournamentKnockout();
        $e->setName('Amateur bracket');
        $e->setTournament($this->getReference('tournament-1'));
        $manager->persist($e);
        $this->addReference('knockout-2', $e);

        $manager->flush();
    }
}
