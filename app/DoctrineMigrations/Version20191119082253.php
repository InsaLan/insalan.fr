<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20191119082253 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_Cosplay (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, team TINYINT(1) NOT NULL, launch INT NOT NULL, setup VARCHAR(255) NOT NULL, details VARCHAR(255) NOT NULL, soundtrack VARCHAR(255) NOT NULL, INDEX IDX_71D660A9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intra_Cosplayer (id INT AUTO_INCREMENT NOT NULL, group_id INT DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, pseudo VARCHAR(255) NOT NULL, usePseudo TINYINT(1) NOT NULL, adult TINYINT(1) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, postalCode INT NOT NULL, characterCosplayed VARCHAR(255) NOT NULL, origin VARCHAR(255) NOT NULL, picturePath VARCHAR(255) NOT NULL, pictureRightPath VARCHAR(255) NOT NULL, parentalConsentPath VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D52F8A23D6E9BEF8 (picturePath), UNIQUE INDEX UNIQ_D52F8A231FCF54DC (pictureRightPath), UNIQUE INDEX UNIQ_D52F8A234F44074B (parentalConsentPath), INDEX IDX_D52F8A23FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_Cosplay ADD CONSTRAINT FK_71D660A9A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_Cosplayer ADD CONSTRAINT FK_D52F8A23FE54D947 FOREIGN KEY (group_id) REFERENCES intra_Cosplay (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Cosplayer DROP FOREIGN KEY FK_D52F8A23FE54D947');
        $this->addSql('DROP TABLE intra_Cosplay');
        $this->addSql('DROP TABLE intra_Cosplayer');
    }
}
