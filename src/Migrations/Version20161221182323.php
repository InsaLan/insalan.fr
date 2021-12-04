<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration: Add Discount entity (amount applied to web and onSite price, linked to specific tournaments) 
 */
class Version20161221182323 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_Discount (id INT AUTO_INCREMENT NOT NULL, tournament_id INT DEFAULT NULL, amount INT NOT NULL, INDEX IDX_685D662133D1A3E7 (tournament_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_Discount ADD CONSTRAINT FK_685D662133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE intra_Discount');
    }
}
