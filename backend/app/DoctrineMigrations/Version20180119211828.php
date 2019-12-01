<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119211828 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Player DROP FOREIGN KEY FK_AD004F246A6289D');
        $this->addSql('ALTER TABLE intra_Discount DROP FOREIGN KEY FK_685D662133D1A3E7');

        $this->addSql('DROP INDEX IDX_AD004F246A6289D ON intra_Player');
        $this->addSql('DROP INDEX IDX_685D662133D1A3E7 ON intra_Discount');

        $this->addSql('ALTER TABLE intra_Player CHANGE pendingtournament_id pendingRegistrable_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Discount CHANGE tournament_id registrable_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE intra_Player ADD CONSTRAINT FK_AD004F2F113F701 FOREIGN KEY (pendingRegistrable_id) REFERENCES intra_Registrable (id)');
        $this->addSql('ALTER TABLE intra_Discount ADD CONSTRAINT FK_685D662141B618A8 FOREIGN KEY (registrable_id) REFERENCES intra_Registrable (id)');

        $this->addSql('CREATE INDEX IDX_AD004F2F113F701 ON intra_Player (pendingRegistrable_id)');
        $this->addSql('CREATE INDEX IDX_685D662141B618A8 ON intra_Discount (registrable_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Player DROP FOREIGN KEY FK_AD004F2F113F701');
        $this->addSql('ALTER TABLE intra_Discount DROP FOREIGN KEY FK_685D662141B618A8');

        $this->addSql('DROP INDEX IDX_AD004F2F113F701 ON intra_Player');
        $this->addSql('DROP INDEX IDX_685D662141B618A8 ON intra_Discount');

        $this->addSql('ALTER TABLE intra_Player CHANGE pendingregistrable_id pendingTournament_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Discount CHANGE registrable_id tournament_id INT DEFAULT NULL');

        $this->addSql('ALTER TABLE intra_Player ADD CONSTRAINT FK_AD004F246A6289D FOREIGN KEY (pendingTournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_Discount ADD CONSTRAINT FK_685D662133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');

        $this->addSql('CREATE INDEX IDX_AD004F246A6289D ON intra_Player (pendingTournament_id)');
        $this->addSql('CREATE INDEX IDX_685D662133D1A3E7 ON intra_Discount (tournament_id)');
    }
}
