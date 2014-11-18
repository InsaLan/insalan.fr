<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141118154028 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE intra_Match ADD koMatch_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Match ADD CONSTRAINT FK_61ABC9C9FFBDC86D FOREIGN KEY (koMatch_id) REFERENCES intra_KnockoutMatch (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_61ABC9C9FFBDC86D ON intra_Match (koMatch_id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE intra_Match DROP FOREIGN KEY FK_61ABC9C9FFBDC86D');
        $this->addSql('DROP INDEX UNIQ_61ABC9C9FFBDC86D ON intra_Match');
        $this->addSql('ALTER TABLE intra_Match DROP koMatch_id');
    }
}
