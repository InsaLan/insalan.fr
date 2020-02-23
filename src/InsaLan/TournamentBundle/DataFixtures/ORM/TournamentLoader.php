<?php
namespace InsaLan\TournamentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use InsaLan\TournamentBundle\Entity\Tournament;
use InsaLan\UserBundle\Service\LoginPlatform;

class TournamentLoader extends AbstractFixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $e = new Tournament();
        $e->setName('CS: GO');
        $e->setShortName('csgo');
        $e->setPlacement(true);
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(64);
        $e->setTeamMaxPlayer(8);
        $e->setTeamMinPlayer(5);
        $e->setWebPrice(22);
        $e->setOnlineIncreaseInPrice(1);
        $e->setOnSitePrice(30);
        $e->setCurrency('EUR');
        $e->setTournamentOpen((new \DateTime())->modify('+90 day'));
        $e->setTournamentClose((new \DateTime())->modify('+93 day'));
        $e->setType('manual');
        $e->setLogoPath('fixtures-1.png');
        $e->setParticipantType('player');
		$e->setLoginType(LoginPlatform::PLATFORM_STEAM);

        $manager->persist($e);
        $this->addReference('tournament-1', $e);

        $e = new Tournament();
        $e->setName('League of Legends');
        $e->setShortName('lol');
        $e->setPlacement(true);
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(64);
        $e->setTeamMaxPlayer(8);
        $e->setTeamMinPlayer(5);
        $e->setWebPrice(22);
        $e->setOnlineIncreaseInPrice(1);
        $e->setOnSitePrice(30);
        $e->setCurrency('EUR');
        $e->setTournamentOpen((new \DateTime())->modify('+90 day'));
        $e->setTournamentClose((new \DateTime())->modify('+93 day'));
        $e->setType('lol');
        $e->setLogoPath('fixtures-2.png');
        $e->setParticipantType('team');
		$e->setLoginType(LoginPlatform::PLATFORM_OTHER);

        $manager->persist($e);
        $this->addReference('tournament-2', $e);

        $e = new Tournament();
        $e->setName('StarCraft II');
        $e->setShortName('st2');
        $e->setPlacement(true);
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(1);
        $e->setTeamMaxPlayer(1);
        $e->setTeamMinPlayer(1);
        $e->setWebPrice(22);
        $e->setOnlineIncreaseInPrice(1);
        $e->setOnSitePrice(30);
        $e->setCurrency('EUR');
        $e->setTournamentOpen((new \DateTime())->modify('+90 day'));
        $e->setTournamentClose((new \DateTime())->modify('+93 day'));
        $e->setType('manual');
        $e->setLogoPath('fixtures-3.png');
        $e->setParticipantType('player');
		$e->setLoginType(LoginPlatform::PLATFORM_BATTLENET);

        $manager->persist($e);
        $this->addReference('tournament-3', $e);

        $e = new Tournament();
        $e->setName('TF2');
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(1);
        $e->setTeamMaxPlayer(1);
        $e->setTeamMinPlayer(1);
        $e->setWebPrice(22);
        $e->setOnlineIncreaseInPrice(1);
        $e->setOnSitePrice(30);
        $e->setCurrency('EUR');
        $e->setLocked('ItCouldBeAPassKey');
        $e->setTournamentOpen((new \DateTime())->modify('+90 day'));
        $e->setTournamentClose((new \DateTime())->modify('+93 day'));
        $e->setType('manual');
        $e->setLogoPath('fixtures-4.png');
        $e->setParticipantType('team');
		$e->setLoginType(LoginPlatform::PLATFORM_STEAM);

        $manager->persist($e);
        $this->addReference('tournament-4', $e);

        $e = new Tournament();
        $e->setName('Fortnite');
        $e->setShortName('fbr');
        $e->setPlacement(true);
        $e->setRegistrationOpen(new \DateTime());
        $e->setRegistrationClose((new \DateTime())->modify('+3 day'));
        $e->setRegistrationLimit(28);
        $e->setTeamMaxPlayer(4);
        $e->setTeamMinPlayer(4);
        $e->setWebPrice(22);
        $e->setOnlineIncreaseInPrice(1);
        $e->setOnSitePrice(30);
        $e->setCurrency('EUR');
        $e->setTournamentOpen((new \DateTime())->modify('+90 day'));
        $e->setTournamentClose((new \DateTime())->modify('+93 day'));
        $e->setType('fbr');
        $e->setLogoPath('fixtures-5.png');
        $e->setParticipantType('team');
        $e->setLoginType(LoginPlatform::PLATFORM_OTHER);

        $manager->persist($e);
        $this->addReference('tournament-5', $e);

        $e = new Tournament();
        $e->setName('CS: GO open forever');
        $e->setShortName('csgo-of');
        $e->setPlacement(true);
        $e->setRegistrationOpen((new \DateTime())->modify('-2 day'));
        $e->setRegistrationClose((new \DateTime())->modify('-1 day'));
        $e->setRegistrationLimit(64);
        $e->setTeamMaxPlayer(8);
        $e->setTeamMinPlayer(5);
        $e->setWebPrice(22);
        $e->setOnlineIncreaseInPrice(1);
        $e->setOnSitePrice(30);
        $e->setCurrency('EUR');
        $e->setTournamentOpen((new \DateTime()));
        $e->setTournamentClose((new \DateTime())->modify('+300 day'));
        $e->setType('manual');
        $e->setLogoPath('fixtures-1.png');
        $e->setParticipantType('player');
		$e->setLoginType(LoginPlatform::PLATFORM_STEAM);

        $manager->persist($e);
        $this->addReference('tournament-6', $e);

        $manager->flush();
    }
}
