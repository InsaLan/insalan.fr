<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\ArchivesStream;

class ArchivesStreamLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new ArchivesStream();
        $e->setName('Lien youtube');
        $e->setUrl('https://www.youtube.com/embed/UwAOFfFiyi8');
        $e->setEdition($this->getReference('edition-1'));
        $e->setAlbum('InsaLan XV');
        $manager->persist($e);

        $e = new ArchivesStream();
        $e->setName('Lien twitch');
        $e->setUrl('https://player.twitch.tv/?enableExtensions=true&muted=false&parent=127.0.0.1&t=0h0m0s&video=46746185&volume=0.87');
        $e->setEdition($this->getReference('edition-1'));
        $e->setAlbum('InsaLan XV');
        $manager->persist($e);

        $e = new ArchivesStream();
        $e->setName('Lien dailymotion');
        $e->setUrl('https://www.dailymotion.com/embed/video/x5bhzt1');
        $e->setEdition($this->getReference('edition-1'));
        $e->setAlbum('InsaLan XV');
        $manager->persist($e);

        $manager->flush();
    }

    public function getOrder()
    {
        return 2;
    }
}

