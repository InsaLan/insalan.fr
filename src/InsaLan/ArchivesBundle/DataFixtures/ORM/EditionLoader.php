<?php
namespace InsaLan\ArchivesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\ArchivesBundle\Entity\Edition;

class EditionLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Edition();
        $e->setName('Edition XIV');
        $e->setYear(2019);
        $e->setImage('InsaLan_14.jpg');
        $e->setTrailerUrl('https://www.youtube.com/embed/_a-zRFnW-bc');
        $e->setTrailerAvailable(true);

        $manager->persist($e);
        $this->addReference('edition-14', $e);

        $e = new Edition();
        $e->setName('Edition XI');
        $e->setYear(2016);
        $e->setImage('InsaLan_11.jpg');
        $e->setTrailerUrl('https://www.youtube.com/embed/SKTF3-J4e5k');
        $e->setTrailerAvailable(true);

        $manager->persist($e);
        $this->addReference('edition-11', $e);
        
        $e = new Edition();
        $e->setName('Edition X');
        $e->setYear(2015);
        $e->setImage('InsaLan_10.jpg');
        $e->setTrailerUrl('https://www.youtube.com/embed/5BHIX03JMp4');
        $e->setTrailerAvailable(true);
        $e->setAftermovieUrl('https://www.youtube.com/embed/2J6dFro2Bno');

        $manager->persist($e);
        $this->addReference('edition-10', $e);
        
        $e = new Edition();
        $e->setName('Edition IV');
        $e->setYear(2009);
        $e->setImage('InsaLan_4.png');
        $e->setTrailerAvailable(false);

        $manager->persist($e);
        $this->addReference('edition-4', $e);
        
        $manager->flush();
    }
}
