<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration to separate Team password salt from Team name
 */
class Version20160814000824 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Team ADD passwordSalt VARCHAR(40) NOT NULL');

        // Team created before this update will still use the salt based on Team name
        $this->addSql('UPDATE `intra_Team` SET `passwordSalt` = SHA1(CONCAT(\'pleaseHashPasswords\', `name`));');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Team DROP passwordSalt');
    }
}
