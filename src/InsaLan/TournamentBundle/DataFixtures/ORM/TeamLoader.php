<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Team;
use InsaLan\UserBundle\Entity\User;

class TeamLoader extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     *       {@inheritDoc}
     */           
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
    public function getOrder()
    {
        return 2;
    }

    public function load(ObjectManager $manager)
    {

        $factory = $this->container->get('security.encoder_factory');
        $encoder = $factory->getEncoder(new User());

        for ($i = 0; $i <= 60; $i++) {
            $t = new Team();
            $name = "Tondeuse Gaming $i";
            $t->setName("Tondeuse Gaming $i");
            $t->setPassword($encoder->encodePassword('hello', sha1('pleaseHashPasswords'.$name)));
            $t->setValidated(2);
            $t->setTournament($this->getReference('tournament-2'));
            $this->addReference('team-'.$i, $t);
            $manager->persist($t);
        }


        $manager->flush();
    }
}
