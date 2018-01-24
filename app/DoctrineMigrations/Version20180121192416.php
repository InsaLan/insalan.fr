<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180121192416 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_merchantorder_player (merchantorder_id INT NOT NULL, player_id INT NOT NULL, INDEX IDX_150E963820E9F9DE (merchantorder_id), INDEX IDX_150E963899E6F5DF (player_id), PRIMARY KEY(merchantorder_id, player_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_merchantorder_player ADD CONSTRAINT FK_150E963820E9F9DE FOREIGN KEY (merchantorder_id) REFERENCES intra_MerchantOrder (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_merchantorder_player ADD CONSTRAINT FK_150E963899E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_MerchantOrder DROP FOREIGN KEY FK_C0C5840A99E6F5DF');
        $this->addSql('DROP INDEX IDX_C0C5840A99E6F5DF ON intra_MerchantOrder');

        $this->addSql('INSERT INTO intra_merchantorder_player (merchantorder_id, player_id)
                       SELECT id, player_id
                       FROM intra_MerchantOrder
        ');

        $this->addSql('ALTER TABLE intra_MerchantOrder DROP player_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE intra_merchantorder_player');
        $this->addSql('ALTER TABLE intra_MerchantOrder ADD player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_MerchantOrder ADD CONSTRAINT FK_C0C5840A99E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id)');
        $this->addSql('CREATE INDEX IDX_C0C5840A99E6F5DF ON intra_MerchantOrder (player_id)');
    }
}
