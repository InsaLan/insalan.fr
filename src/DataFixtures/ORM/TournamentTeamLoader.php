<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\PizzaOrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Team;
use App\Entity\User;

class TournamentTeamLoader extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
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
            $t->generatePasswordSalt();
            $t->setPassword($encoder->encodePassword('hello', $t->getPasswordSalt()));
            $t->setValidated(2);
            $t->setTournament($this->getReference('tournament-2'));
            $this->addReference('team-'.$i, $t);
            $manager->persist($t);
        }

        for ($i = 1; $i <= 28; $i++) {
            $t = new Team();
            $t->setName("Tondeuse Royale $i");
            $t->generatePasswordSalt();
            $t->setPassword($encoder->encodePassword('hello', $t->getPasswordSalt()));
            $t->setValidated(0);
            $t->setTournament($this->getReference('tournament-5'));
            $this->addReference('royal-team-'.$i, $t);
            $manager->persist($t);
        }


        $manager->flush();
    }
}
