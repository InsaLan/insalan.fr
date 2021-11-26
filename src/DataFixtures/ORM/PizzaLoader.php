<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Pizza;

class PizzaLoader extends AbstractFixture implements OrderedFixtureInterface
{

    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Pizza();
        $e->setName('Reine');
        $e->setDescription('Tomate, Jambon, Mozzarella');
        $e->setPrice(8);
        $e->setVeggie(false);
        $this->addReference('pizza-1', $e);
        $manager->persist($e);

        $e = new Pizza();
        $e->setName('Campagnarde');
        $e->setDescription('Andouillette, Oignons, Reblochon');
        $e->setPrice(10);
        $e->setVeggie(false);
        $this->addReference('pizza-2', $e);
        $manager->persist($e);

        $e = new Pizza();
        $e->setName('Mystère');
        $e->setPrice(15);
        $e->setDescription("");
        $e->setVeggie(false);
        $this->addReference('pizza-3', $e);
        $manager->persist($e);

        $manager->flush();
    }
}
