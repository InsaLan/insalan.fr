<?php
namespace InsaLan\CosplayBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\CosplayBundle\Entity\Cosplay;

class CosplayLoader extends AbstractFixture implements OrderedFixtureInterface
{   

    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {   

        $e = new Cosplay();
        $e->setUser($this->getReference('user-1'));
        $e->setName("Les tortues ninja");
        $e->setTeam(true);
        $e->setLaunch(Cosplay::LAUNCH_BEFORE);
        $e->setSetup("text");
        $e->setDetails("text");
        $e->setSoundtrack("path1");
        $this->addReference('cosplay-1', $e);
        $manager->persist($e);

        $e = new Cosplay();
        $e->setUser($this->getReference('user-2'));
        $e->setName("Naruto");
        $e->setTeam(false);
        $e->setLaunch(Cosplay::LAUNCH_AFTER);
        $e->setSetup("text");
        $e->setDetails("text");
        $e->setSoundtrack("path2");
        $this->addReference('cosplay-2', $e);
        $manager->persist($e);

        $manager->flush();
    }
}