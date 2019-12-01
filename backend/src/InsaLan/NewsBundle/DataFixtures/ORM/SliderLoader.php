<?php
namespace InsaLan\NewsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\NewsBundle\Entity\Slider;

class SliderLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new Slider();
        $e->setTitle('Experience a whole new digital world');
        $e->setSubtitle('Video games as you never expected them');
        $e->setLink('http://www.insalan.fr/');
        $manager->persist($e);

        $e = new Slider();
        $e->setTitle('Second slider');
        $e->setSubtitle('That one is flipped');
        $e->setLink('http://perdu.com/');
        $manager->persist($e);

        $manager->flush();
    }
}
