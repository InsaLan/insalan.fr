<?php
namespace InsaLan\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\PizzaBundle\Entity\Pizza;

class PizzaLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new Pizza();
        $e->setName('Reine');
        $e->setPrice(8);
        $e->setDescription('');
        $manager->persist($e);

        $e = new Pizza();
        $e->setName('Campagnarde');
        $e->setPrice(8);
        $e->setDescription('');
        $manager->persist($e);

        $manager->flush();
    }
}

