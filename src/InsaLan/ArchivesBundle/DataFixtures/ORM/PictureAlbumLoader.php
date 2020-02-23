<?php
namespace InsaLan\ArchivesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\ArchivesBundle\Entity\PictureAlbum;

class PictureAlbumLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {
        $e = new PictureAlbum();
        $e->setName('Photo InsaLan XIV');
        $e->setUrl('https://www.flickr.com/gp/168447702@N02/Kcqz46');
        $e->setEdition($this->getReference('edition-14'));

        $manager->persist($e);

        $manager->flush();
    }
}
