<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151230115740 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Manager ADD tournament_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Manager ADD CONSTRAINT FK_94DDEA2033D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('CREATE INDEX IDX_94DDEA2033D1A3E7 ON intra_Manager (tournament_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Manager DROP FOREIGN KEY FK_94DDEA2033D1A3E7');
        $this->addSql('DROP INDEX IDX_94DDEA2033D1A3E7 ON intra_Manager');
        $this->addSql('ALTER TABLE intra_Manager DROP tournament_id');
    }
}
