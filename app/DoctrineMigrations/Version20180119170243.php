<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119170243 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_Bundle (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intra_bundle_tournament (bundle_id INT NOT NULL, tournament_id INT NOT NULL, INDEX IDX_2FD0E1B6F1FAD9D3 (bundle_id), INDEX IDX_2FD0E1B633D1A3E7 (tournament_id), PRIMARY KEY(bundle_id, tournament_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_Bundle ADD CONSTRAINT FK_37B24C6ABF396750 FOREIGN KEY (id) REFERENCES intra_Registrable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_bundle_tournament ADD CONSTRAINT FK_2FD0E1B6F1FAD9D3 FOREIGN KEY (bundle_id) REFERENCES intra_Bundle (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_bundle_tournament ADD CONSTRAINT FK_2FD0E1B633D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_bundle_tournament DROP FOREIGN KEY FK_2FD0E1B6F1FAD9D3');
        $this->addSql('DROP TABLE intra_Bundle');
        $this->addSql('DROP TABLE intra_bundle_tournament');
    }
}
