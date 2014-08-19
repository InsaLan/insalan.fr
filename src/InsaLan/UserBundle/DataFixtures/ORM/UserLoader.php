<?php
namespace InsaLan\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use InsaLan\UserBundle\Entity\User;

class UserLoader implements FixtureInterface, ContainerAwareInterface, OrderedFixtureInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $e = new User();
        $e->setUsername('admin');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($e);
        $e->setEmail('admin@localhost');
        $e->setPassword($encoder->encodePassword('admin', $e->getSalt()));
        $e->setEnabled(true);
        $e->addRole('ROLE_ADMIN');
        $manager->persist($e);

        $e = new User();
        $e->setUsername('user');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($e);
        $e->setEmail('user@localhost');
        $e->setPassword($encoder->encodePassword('user', $e->getSalt()));
        $e->setEnabled(true);
        $e->addRole('ROLE_USER');
        $manager->persist($e);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
