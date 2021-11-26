<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\PizzaOrder;

class PizzaOrderLoader extends AbstractFixture implements OrderedFixtureInterface
{   

    public function getOrder()
    {
        return 1;
    }

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
        $e->setCapacity(60);
        $e->setForeignCapacity(5);
        $this->addReference('order-1', $e);
        $manager->persist($e);

        $e = new Order();
        $e->setExpiration($h2);
        $e->setDelivery($h3);
        $e->setCapacity(3);
        $e->setForeignCapacity(1);
        $this->addReference('order-2', $e);
        $manager->persist($e);

        $manager->flush();
    }
}