<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20161215182449 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_MerchantOrder (id INT AUTO_INCREMENT NOT NULL, merchant_id INT DEFAULT NULL, payment_id INT DEFAULT NULL, player_id INT DEFAULT NULL, INDEX IDX_C0C5840A6796D554 (merchant_id), INDEX IDX_C0C5840A4C3A3BB (payment_id), INDEX IDX_C0C5840A99E6F5DF (player_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_MerchantOrder ADD CONSTRAINT FK_C0C5840A6796D554 FOREIGN KEY (merchant_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_MerchantOrder ADD CONSTRAINT FK_C0C5840A4C3A3BB FOREIGN KEY (payment_id) REFERENCES intra_payum_payment_details (id)');
        $this->addSql('ALTER TABLE intra_MerchantOrder ADD CONSTRAINT FK_C0C5840A99E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE intra_MerchantOrder');
    }
}
