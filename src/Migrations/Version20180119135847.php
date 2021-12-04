<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180119135847 extends AbstractMigration
{

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Create and populate Registrable using current Tournament data
        $this->addSql('CREATE TABLE intra_Registrable (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, registrationOpen DATETIME NOT NULL, registrationClose DATETIME NOT NULL, registrationLimit INT NOT NULL, locked VARCHAR(255) DEFAULT NULL, logoPath VARCHAR(255) DEFAULT NULL, webPrice NUMERIC(10, 2) NOT NULL, onlineIncreaseInPrice NUMERIC(10, 2) NOT NULL, onSitePrice NUMERIC(10, 2) NOT NULL, currency VARCHAR(255) NOT NULL, kind VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $this->addSql('INSERT INTO intra_Registrable(
                kind,
                id, name, description,
                registrationOpen, registrationClose, registrationLimit,
                locked, logoPath,
                webPrice, onlineIncreaseInPrice, onSitePrice, currency
            )
            SELECT
                "tournament" as kind,
                id, name, description,
                registrationOpen, registrationClose, registrationLimit,
                locked, logoPath,
                webPrice, onlineIncreaseInPrice, onSitePrice, currency
            FROM intra_Tournament');

        // Remove constraints to Tournament so we can alter the table
        $this->addSql('ALTER TABLE intra_Discount DROP FOREIGN KEY FK_685D662133D1A3E7');
        $this->addSql('ALTER TABLE intra_Participant DROP FOREIGN KEY FK_E25002E933D1A3E7');
        $this->addSql('ALTER TABLE intra_Manager DROP FOREIGN KEY FK_94DDEA2033D1A3E7');
        $this->addSql('ALTER TABLE intra_Knockout DROP FOREIGN KEY FK_372BF28833D1A3E7');
        $this->addSql('ALTER TABLE intra_Player DROP FOREIGN KEY FK_AD004F246A6289D');
        $this->addSql('ALTER TABLE intra_GroupStage DROP FOREIGN KEY FK_D03952E33D1A3E7');

        // Update table schema
        $this->addSql('ALTER TABLE intra_Tournament CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE intra_Tournament ADD CONSTRAINT FK_C79EBAF1BF396750 FOREIGN KEY (id) REFERENCES intra_Registrable (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE intra_Tournament DROP name, DROP description, DROP registrationOpen, DROP registrationClose, DROP registrationLimit, DROP logoPath, DROP webPrice, DROP locked, DROP onlineIncreaseInPrice, DROP currency, DROP onSitePrice');

        // Restore constraints
        $this->addSql('ALTER TABLE intra_Discount ADD CONSTRAINT FK_685D662133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_Participant ADD CONSTRAINT FK_E25002E933D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_Manager ADD CONSTRAINT FK_94DDEA2033D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_Knockout ADD CONSTRAINT FK_372BF28833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE intra_Player ADD CONSTRAINT FK_AD004F246A6289D FOREIGN KEY (pendingTournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_GroupStage ADD CONSTRAINT FK_D03952E33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // Remove constraints to Tournament so we can alter the table
        $this->addSql('ALTER TABLE intra_Discount DROP FOREIGN KEY FK_685D662133D1A3E7');
        $this->addSql('ALTER TABLE intra_Participant DROP FOREIGN KEY FK_E25002E933D1A3E7');
        $this->addSql('ALTER TABLE intra_Manager DROP FOREIGN KEY FK_94DDEA2033D1A3E7');
        $this->addSql('ALTER TABLE intra_Knockout DROP FOREIGN KEY FK_372BF28833D1A3E7');
        $this->addSql('ALTER TABLE intra_Player DROP FOREIGN KEY FK_AD004F246A6289D');
        $this->addSql('ALTER TABLE intra_GroupStage DROP FOREIGN KEY FK_D03952E33D1A3E7');

        // Update table schema
        $this->addSql('ALTER TABLE intra_Tournament DROP FOREIGN KEY FK_C79EBAF1BF396750');
        $this->addSql('ALTER TABLE intra_Tournament CHANGE id id INT AUTO_INCREMENT NOT NULL');

        $this->addSql('ALTER TABLE intra_Tournament ADD name VARCHAR(50) NOT NULL, ADD description VARCHAR(255) DEFAULT NULL, ADD registrationOpen DATETIME NOT NULL, ADD registrationClose DATETIME NOT NULL, ADD registrationLimit INT NOT NULL, ADD logoPath VARCHAR(255) DEFAULT NULL, ADD webPrice NUMERIC(10, 2) NOT NULL, ADD locked VARCHAR(255) DEFAULT NULL, ADD onlineIncreaseInPrice NUMERIC(10, 2) NOT NULL, ADD currency VARCHAR(255) NOT NULL, ADD onSitePrice NUMERIC(10, 2) NOT NULL');

        // Restore Tournament values from Registrable
        $this->addSql('UPDATE intra_Tournament as t0, intra_Registrable as t1 SET
                t0.name = t1.name,
                t0.description = t1.description,
                t0.registrationOpen = t1.registrationOpen,
                t0.registrationClose = t1.registrationClose,
                t0.registrationLimit = t1.registrationLimit,
                t0.locked = t1.locked,
                t0.logoPath = t1.logoPath,
                t0.webPrice = t1.webPrice,
                t0.onlineIncreaseInPrice = t1.onlineIncreaseInPrice,
                t0.onSitePrice = t1.onSitePrice,
                t0.currency = t1.currency
            WHERE t0.id = t1.id');

        // Restore constraints
        $this->addSql('ALTER TABLE intra_Discount ADD CONSTRAINT FK_685D662133D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_Participant ADD CONSTRAINT FK_E25002E933D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_Manager ADD CONSTRAINT FK_94DDEA2033D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_Knockout ADD CONSTRAINT FK_372BF28833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE;');
        $this->addSql('ALTER TABLE intra_Player ADD CONSTRAINT FK_AD004F246A6289D FOREIGN KEY (pendingTournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_GroupStage ADD CONSTRAINT FK_D03952E33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');

        // Drop Registrable table
        $this->addSql('DROP TABLE intra_Registrable');
    }
}
