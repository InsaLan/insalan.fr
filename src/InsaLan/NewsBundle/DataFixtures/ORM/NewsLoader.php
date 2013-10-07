<?php
namespace InsaLan\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\NewsBundle\Entity\News;

class NewsLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new News();
        $e->setTitle('Foreign studies suck');
        $e->setText('C\'est loin le Luxembourg :(');
        $manager->persist($e);

        $e = new News();
        $e->setTitle('Une nouvelle espèce découverte !');
        $e->setText('Serait-ce un beurawoojeoda ?');
        $manager->persist($e);

        $manager->flush();
    }
}
