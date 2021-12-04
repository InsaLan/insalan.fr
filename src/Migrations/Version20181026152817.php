<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181026152817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_GlobalVars (id INT AUTO_INCREMENT NOT NULL, globalKey VARCHAR(255) NOT NULL, globalValue VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intra_Staff (id INT AUTO_INCREMENT NOT NULL, role VARCHAR(255) NOT NULL, firstName VARCHAR(255) NOT NULL, lastName VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // Set Global Vars
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "staffNumber", "90")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "number", "14")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "lettersNumber", "quatorzième")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "playersNumber", "400")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "openingDate", "Samedi 16 Février")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "openingHour", "8h00")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "closingDate", "Dimanche 17 Février")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "closingHour", "19h00")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "price", "30")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "webPrice", "25")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "campanilePrice", "45")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "cosplayEdition", "4")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "cosplayName", "le Japanim Concours Cosplay")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "cosplayDate", "Samedi 24 Février 2018")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "cosplayEndRegistration", "21 Février")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "fullDates", "16 au 17 Février 2019")');
        $this->addSql('INSERT INTO intra_GlobalVars (id, globalKey, globalValue) VALUES (NULL, "payCheckAddress", "")');

        // Set Staff
        $this->addSql('INSERT INTO intra_Staff (id, role, email) VALUES (NULL, "Responsable Partenariat", "partenariat@insalan.fr")');
        $this->addSql('INSERT INTO intra_Staff (id, role, email) VALUES (NULL, "Responsable Tournois", "tournois@insalan.fr")');
        $this->addSql('INSERT INTO intra_Staff (id, role, email) VALUES (NULL, "Responsable Cosplay", "cosplay@insalan.fr")');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE intra_GlobalVars');
        $this->addSql('DROP TABLE intra_Staff');
    }
}
