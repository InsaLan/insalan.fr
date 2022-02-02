<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Reviewed by Lesterpig
 */
class Version20150204232359 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_User DROP table_id');
        $this->addSql('ALTER TABLE intra_UserOrder ADD fullnameCanonical VARCHAR(255) DEFAULT NULL, ADD usernameCanonical VARCHAR(255) DEFAULT NULL, ADD type INT NOT NULL, ADD price INT NOT NULL, ADD paymentDone TINYINT(1) NOT NULL, ADD foreign_user TINYINT(1) NOT NULL, ADD createdAt DATETIME NOT NULL, ADD updatedAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE intra_Order ADD foreignCapacity INT NOT NULL, ADD createdAt DATETIME NOT NULL, ADD updatedAt DATETIME NOT NULL, ADD closed TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Order DROP foreignCapacity, DROP createdAt, DROP updatedAt, DROP closed');
        $this->addSql('ALTER TABLE intra_User ADD table_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_UserOrder DROP fullnameCanonical, DROP usernameCanonical, DROP type, DROP price, DROP paymentDone, DROP foreign_user, DROP createdAt, DROP updatedAt');
    }
}
