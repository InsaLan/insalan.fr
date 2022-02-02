<?php
namespace App\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\InsaLanGlobalVars;

class InsaLanGlobalVarsLoader implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('staffNumber');
        $e->setGlobalValue('90');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('number');
        $e->setGlobalValue('14');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('lettersNumber');
        $e->setGlobalValue('quatorzième');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('romanNumber');
        $e->setGlobalValue('XIV');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('playersNumber');
        $e->setGlobalValue('400');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('openingDate');
        $e->setGlobalValue('Samedi 16 Février');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('openingHour');
        $e->setGlobalValue('8h00');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('closingDate');
        $e->setGlobalValue('Dimanche 17 Février');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('closingHour');
        $e->setGlobalValue('19h00');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('price');
        $e->setGlobalValue('30');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('webPrice');
        $e->setGlobalValue('25');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('campanilePrice');
        $e->setGlobalValue('45');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('cosplayEdition');
        $e->setGlobalValue('5');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('cosplayName');
        $e->setGlobalValue('InsaLan Concours Cosplay');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('cosplayDate');
        $e->setGlobalValue('Samedi 16 Février 2019');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('cosplayEndRegistration');
        $e->setGlobalValue('15 Février');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('fullDates');
        $e->setGlobalValue('16 au 17 Février 2019');
        $manager->persist($e);

        $e = new InsaLanGlobalVars();
        $e->setGlobalKey('payCheckAddress');
        $e->setGlobalValue('Oncle Picsou<br>5 rue des Argentiers<br>35000, Rennes<br>');
        $manager->persist($e);

        $manager->flush();
    }
}
