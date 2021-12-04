<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190301084100 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `intra_PictureAlbumArchives` (id INT AUTO_INCREMENT NOT NULL, edition_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_665B43B574281A5E (edition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `intra_PictureAlbumArchives` ADD CONSTRAINT FK_665B43B574281A5E FOREIGN KEY (edition_id) REFERENCES intra_Edition (id)');
        $this->addSql('DROP TABLE intra_PictureArchives');
        $this->addSql('ALTER TABLE intra_StreamArchives ADD edition_id INT DEFAULT NULL, DROP date, CHANGE album album VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE intra_Edition ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE intra_StreamArchives ADD CONSTRAINT FK_3D06A97374281A5E FOREIGN KEY (edition_id) REFERENCES intra_Edition (id)');
        $this->addSql('CREATE INDEX IDX_3D06A97374281A5E ON intra_StreamArchives (edition_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_PictureArchives (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, album VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE `intra_PictureAlbumArchives`');
        $this->addSql('ALTER TABLE intra_StreamArchives DROP FOREIGN KEY FK_3D06A97374281A5E');
        $this->addSql('DROP INDEX IDX_3D06A97374281A5E ON intra_StreamArchives');
        $this->addSql('ALTER TABLE intra_StreamArchives ADD date DATETIME NOT NULL, DROP edition_id, CHANGE album album VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Edition DROP name');
    }
}
