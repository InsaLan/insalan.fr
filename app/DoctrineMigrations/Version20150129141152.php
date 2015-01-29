<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Reviewed by Lesterpig
 */
class Version20150129141152 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Tournament ADD playerInfos LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Knockout ADD doubleElimination TINYINT(1) NOT NULL');
        $this->addSql('UPDATE intra_Knockout SET doubleElimination = 0');
        $this->addSql('ALTER TABLE intra_KnockoutMatch ADD oddNode TINYINT(1) NOT NULL, ADD loserDestination_id INT DEFAULT NULL');
        $this->addSql('UPDATE intra_KnockoutMatch SET oddNode = 0');
        $this->addSql('ALTER TABLE intra_KnockoutMatch ADD CONSTRAINT FK_B27CC64B7A6E108 FOREIGN KEY (loserDestination_id) REFERENCES intra_KnockoutMatch (id)');
        $this->addSql('CREATE INDEX IDX_B27CC64B7A6E108 ON intra_KnockoutMatch (loserDestination_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE intra_Knockout DROP doubleElimination');
        $this->addSql('ALTER TABLE intra_KnockoutMatch DROP FOREIGN KEY FK_B27CC64B7A6E108');
        $this->addSql('DROP INDEX IDX_B27CC64B7A6E108 ON intra_KnockoutMatch');
        $this->addSql('ALTER TABLE intra_KnockoutMatch DROP oddNode, DROP loserDestination_id');
        $this->addSql('ALTER TABLE intra_Tournament DROP playerInfos');
    }
}
