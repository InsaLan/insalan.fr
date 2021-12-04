<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20151222161500 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_Manager (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, participant_id INT DEFAULT NULL, game_name VARCHAR(50) NOT NULL, payment_done TINYINT(1) NOT NULL, arrived TINYINT(1) NOT NULL, INDEX IDX_94DDEA20A76ED395 (user_id), UNIQUE INDEX UNIQ_94DDEA20BBABFC4A (participant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_Manager ADD CONSTRAINT FK_94DDEA20A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_Manager ADD CONSTRAINT FK_94DDEA20BBABFC4A FOREIGN KEY (participant_id) REFERENCES intra_Participant (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $this->addSql('DROP TABLE intra_Manager');
    }
}
