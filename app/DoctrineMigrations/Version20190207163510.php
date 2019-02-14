<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190207163510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        $this->addSql('CREATE TABLE royalmatch_participant (royalmatch_id INT NOT NULL, participant_id INT NOT NULL, INDEX IDX_E36976DFE0726F1B (royalmatch_id), INDEX IDX_E36976DF9D1C3019 (participant_id), PRIMARY KEY(royalmatch_id, participant_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE royalmatch_participant ADD CONSTRAINT FK_E36976DFE0726F1B FOREIGN KEY (royalmatch_id) REFERENCES intra_Match (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE royalmatch_participant ADD CONSTRAINT FK_E36976DF9D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');    
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('DROP TABLE royalmatch_participant');
    }
}
