<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\InsaLanStaff;

class InsaLanStaffLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new InsaLanStaff();
        $e->setRole('Resp Com');
        $e->setFirstName('John');
        $e->setLastName('Doe');
        $e->setPhone('+33 (0)6 33 66 99 00');
        $e->setEmail('com@insalan.fr');
        $manager->persist($e);

        $e = new InsaLanStaff();
        $e->setRole('Resp Tournois');
        $e->setFirstName('Jane');
        $e->setLastName('Doe');
        $e->setPhone('+33 (0)6 33 11 44 00');
        $e->setEmail('tourn@insalan.fr');
        $manager->persist($e);

        $manager->flush();
    }
}
