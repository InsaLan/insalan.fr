<?php
namespace InsaLan\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\PizzaBundle\Entity\Order;

class OrderLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {   

        $h1 = new \Datetime();
        $h2 = new \Datetime();
        $h3 = new \Datetime();
        $h1->modify("+1 hour");
        $h2->modify("+2 hour");
        $h3->modify("+3 hour");

        $e = new Order();
        $e->setExpiration($h1);
        $e->setDelivery($h2);
        $e->setCapacity(10);
        $manager->persist($e);

        $e = new Order();
        $e->setExpiration($h2);
        $e->setDelivery($h3);
        $e->setCapacity(20);
        $manager->persist($e);

        $manager->flush();
    }
}