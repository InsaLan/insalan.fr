<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180115000249 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $teams = $em->getRepository('InsaLanTournamentBundle:Team')->findAll();

        foreach ($teams as &$t) {
            if ($t->getValidationDate() !== null) continue;

            $t->setValidationDate(new \DateTime('@'.$t->getLastUpdated()));
        }

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
    }
}
