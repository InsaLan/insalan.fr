<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration: Modify PaymentDetail entity to keep track of used discount
 */
class Version20161222144208 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_payum_payment_details ADD discount_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_payum_payment_details ADD CONSTRAINT FK_EAFE1B04C7C611F FOREIGN KEY (discount_id) REFERENCES intra_Discount (id)');
        $this->addSql('CREATE INDEX IDX_EAFE1B04C7C611F ON intra_payum_payment_details (discount_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_payum_payment_details DROP FOREIGN KEY FK_EAFE1B04C7C611F');
        $this->addSql('DROP INDEX IDX_EAFE1B04C7C611F ON intra_payum_payment_details');
        $this->addSql('ALTER TABLE intra_payum_payment_details DROP discount_id');
    }
}
