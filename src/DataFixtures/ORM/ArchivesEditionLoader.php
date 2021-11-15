<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\PizzaOrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\ArchivesEdition;

class ArchivesEditionLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new Edition();
        $e->setName('InsaLan XV');
        $e->setYear(2020);
        $e->setImage('InsaLan_14.jpg');
        $e->setTrailerAvailable(True);
        $e->setTrailerUrl('https://www.youtube.com/embed/UwAOFfFiyi8');
        $manager->persist($e);
        $this->addReference('edition-1', $e);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}

