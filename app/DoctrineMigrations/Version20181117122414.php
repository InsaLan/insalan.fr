<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

use InsaLan\UserBundle\Entity\PaymentDetails;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181117122414 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_payum_payment_details ADD place INT NOT NULL, ADD type INT NOT NULL, ADD rawPrice DOUBLE PRECISION NOT NULL');
    }

    public function postUp(Schema $schema)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');

        $PaymentDetails = $em->getRepository('InsaLanUserBundle:PaymentDetails')->findAll();

        foreach ($PaymentDetails as &$p) {
            $details = $p->getDetails();
            $rawPrice = $details['L_PAYMENTREQUEST_0_AMT0'];
            $type = PaymentDetails::TYPE_UNDEFINED;
            $place = PaymentDetails::PLACE_UNDEFINED;

            // Check if payment was made with paypal
            if ($details['L_PAYMENTREQUEST_0_NAME1']=='Majoration paiement en ligne') {
                $type = PaymentDetails::TYPE_PAYPAL;
                $place = PaymentDetails::PLACE_WEB;
            } else if ($details['L_PAYMENTREQUEST_0_DESC1']=='Paiement validé par Warp Zone') { // Check if payment was made in WarpZone
                $place = PaymentDetails::PLACE_IN_PARTNER_SHOP;
            } else { // Last option is pre ordering by check
                $type = PaymentDetails::TYPE_CHECK;
                $place = PaymentDetails::PLACE_WEB;           
              }
            $p->setPlace($place);
            $p->setType($type);
            $p->setRawPrice($rawPrice);
        }

        $em->flush();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_payum_payment_details DROP place, DROP type, DROP rawPrice');
    }
}
