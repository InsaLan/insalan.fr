<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Entity\User;

class UserLoader extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        $this->addReference('user-1', $e);
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
        $this->addReference('user-2', $e);
        $manager->persist($e);

        $e = new User();
        $e->setUsername('merchant');
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($e);
        $e->setEmail('merchant@localhost');
        $e->setPassword($encoder->encodePassword('merchant', $e->getSalt()));
        $e->setEnabled(true);
        $e->addRole('ROLE_MERCHANT');
        $this->addReference('user-3', $e);
        $manager->persist($e);

        for ($i = 1; $i <= 305; ++$i) {
            $e = new User();
            $e->setUsername('user'.$i);
            $encoder = $this->container
                ->get('security.encoder_factory')
                ->getEncoder($e);
            $e->setEmail('user'.$i.'@localhost');
            $e->setPassword($encoder->encodePassword('user', $e->getSalt()));
            $e->setEnabled(true);
            $e->addRole('ROLE_USER');
            $manager->persist($e);
            $this->addReference('user--'.$i, $e);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}
