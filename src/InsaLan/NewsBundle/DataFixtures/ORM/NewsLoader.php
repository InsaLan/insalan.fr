<?php
namespace InsaLan\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\NewsBundle\Entity\News;
use \DateTime;

class NewsLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new News();
        $e->setTitle('Lorem ipsum ');
        $e->setCategory('InsaLan');
        $e->setCreatedAt(new \DateTime('2014-08-12 11:14:12'));
        $e->setUpdatedAt(new \DateTime('2014-02-12 11:11:11'));
        $e->setText('Lorem ipsum dolor sit amet, consectetur adipisicing elit.
        	Veniam, animi, recusandae, cupiditate obcaecati minus dignissimos
        	ut debitis error quae asperiores voluptate fugit aliquam molestiae
        	perspiciatis nisi modi sequi laboriosam similique.');
        $manager->persist($e);

        $e = new News();
        $e->setTitle('Une nouvelle espèce découverte !');
        $e->setCategory('Site internet');
        $e->setText('Serait-ce un beurawoojeoda ?');
        $e->setCreatedAt(new DateTime('2014-08-12 11:14:12'));
        $e->setUpdatedAt(new DateTime('2014-02-12 11:11:11'));
        $manager->persist($e);

        $manager->flush();
    }
}
