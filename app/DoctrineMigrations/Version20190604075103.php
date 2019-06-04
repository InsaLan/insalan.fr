<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190604075103 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_e_ticket (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, tournament_id INT DEFAULT NULL, token VARCHAR(255) NOT NULL, sentAt DATETIME DEFAULT NULL, isScanned TINYINT(1) NOT NULL, INDEX IDX_7E1D5491A76ED395 (user_id), INDEX IDX_7E1D549133D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_e_ticket ADD CONSTRAINT FK_7E1D5491A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_e_ticket ADD CONSTRAINT FK_7E1D549133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_manager ADD eTicket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_manager ADD CONSTRAINT FK_94DDEA20C0BC769D FOREIGN KEY (eTicket_id) REFERENCES intra_e_ticket (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94DDEA20C0BC769D ON intra_manager (eTicket_id)');
        $this->addSql('ALTER TABLE intra_player ADD eTicket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_player ADD CONSTRAINT FK_AD004F2C0BC769D FOREIGN KEY (eTicket_id) REFERENCES intra_e_ticket (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AD004F2C0BC769D ON intra_player (eTicket_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Manager DROP FOREIGN KEY FK_94DDEA20C0BC769D');
        $this->addSql('ALTER TABLE intra_Player DROP FOREIGN KEY FK_AD004F2C0BC769D');
        $this->addSql('DROP TABLE intra_e_ticket');
        $this->addSql('DROP INDEX UNIQ_94DDEA20C0BC769D ON intra_Manager');
        $this->addSql('ALTER TABLE intra_Manager DROP eTicket_id');
        $this->addSql('DROP INDEX UNIQ_AD004F2C0BC769D ON intra_Player');
        $this->addSql('ALTER TABLE intra_Player DROP eTicket_id');
    }
}
