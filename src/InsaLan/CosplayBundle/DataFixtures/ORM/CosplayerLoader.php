<?php
namespace InsaLan\CosplayBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\CosplayBundle\Entity\Cosplayer;

class CosplayerLoader extends AbstractFixture implements OrderedFixtureInterface
{   

    public function getOrder()
    {
        return 3;
    }

    public function load(ObjectManager $manager)
    {   

        $e = new Cosplayer();
        $e->setFirstname("Bob");
        $e->setLastname("Martin");
        $e->setPseudo("Rafael");
        $e->setUsePseudo(true);
        $e->setAdult(true);
        $e->setEmail('admin@localhost');
        $e->setPhone("0688885555");
        $e->setPostalCode(35000);
        $e->setCharacterCosplayed("Rafael Ninja");
        $e->setOrigin("Film");
        $e->setPicturePath("path0");
        $e->setPictureRightPath("path3");
        $this->getReference('cosplay-1')->addMember($e);
        $this->addReference('cosplayer-1', $e);
        $manager->persist($e);

        $e = new Cosplayer();
        $e->setFirstname("Bob");
        $e->setLastname("Martin");
        $e->setPseudo("Rafael");
        $e->setUsePseudo(true);
        $e->setAdult(false);
        $e->setEmail('hey@localhost');
        $e->setPhone("0688335555");
        $e->setPostalCode(35000);
        $e->setCharacterCosplayed("Rafael Ninja");
        $e->setOrigin("Film");
        $e->setPicturePath("path8");
        $e->setPictureRightPath("path6");
        $e->setParentalConsentPath("path");
        $this->getReference('cosplay-1')->addMember($e);
        $this->addReference('cosplayer-2', $e);
        $manager->persist($e);

        $e = new Cosplayer();
        $e->setFirstname("Bob");
        $e->setLastname("Martin");
        $e->setPseudo("Rafael");
        $e->setUsePseudo(false);
        $e->setAdult(true);
        $e->setEmail('groot@localhost');
        $e->setPhone("07777777555");
        $e->setPostalCode(35000);
        $e->setCharacterCosplayed("Rafael Ninja");
        $e->setOrigin("Film");
        $e->setPicturePath("path11");
        $e->setPictureRightPath("path1111");
        $this->getReference('cosplay-2')->addMember($e);
        $this->addReference('cosplayer-3', $e);
        $manager->persist($e);
        $manager->flush();
    }
}