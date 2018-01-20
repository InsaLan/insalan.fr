<?php
namespace InsaLan\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\PizzaBundle\Entity\UserOrder;

class UserOrderLoader extends AbstractFixture implements OrderedFixtureInterface
{


    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {

        // Web Users

        $e = new UserOrder();
        $e->setUser($this->getReference('user-1'));
        $e->setPaymentDone(true);
        $e->setPizza($this->getReference('pizza-1'));
        $e->setOrder($this->getReference('order-1'));
        $e->setType(UserOrder::TYPE_PAYPAL);
        $manager->persist($e);

        $e = new UserOrder();
        $e->setUser($this->getReference('user-1'));
        $e->setPaymentDone(true);
        $e->setPizza($this->getReference('pizza-2'));
        $e->setOrder($this->getReference('order-2'));
        $e->setType(UserOrder::TYPE_PAYPAL);
        $manager->persist($e);

        $e = new UserOrder();
        $e->setUser($this->getReference('user-1'));
        $e->setPaymentDone(false);
        $e->setPizza($this->getReference('pizza-3'));
        $e->setOrder($this->getReference('order-2'));
        $e->setType(UserOrder::TYPE_PAYPAL);
        $manager->persist($e);

        // Manual Users
        
        $e = new UserOrder();
        $e->setPaymentDone(true);
        $e->setPizza($this->getReference('pizza-1'));
        $e->setOrder($this->getReference('order-1'));
        $e->setType(UserOrder::TYPE_MANUAL);
        $e->setUsernameCanonical("Aaah");
        $e->setFullnameCanonical("Albert Le Grand");
        $manager->persist($e);

        $e = new UserOrder();
        $e->setPaymentDone(true);
        $e->setPizza($this->getReference('pizza-3'));
        $e->setOrder($this->getReference('order-1'));
        $e->setType(UserOrder::TYPE_MANUAL);
        $e->setUsernameCanonical("Uhuh");
        $manager->persist($e);

        $manager->flush();
    }
}
