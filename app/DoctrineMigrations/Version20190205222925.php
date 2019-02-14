<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190205222925 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_Score (round_id INT NOT NULL, participant_id INT NOT NULL, score INT NOT NULL, INDEX IDX_29693B9DA6005CA0 (round_id), INDEX IDX_29693B9D9D1C3019 (participant_id), PRIMARY KEY(round_id, participant_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE intra_Score ADD CONSTRAINT FK_29693B9DA6005CA0 FOREIGN KEY (round_id) REFERENCES intra_Round (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_Score ADD CONSTRAINT FK_29693B9D9D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        
        $this->addSql('INSERT INTO
            intra_Score(
                round_id,
                participant_id,
                score
            )
            SELECT
                intra_Round.id as round_id,
                part1_id as participant_id,
                score1 as score
            FROM intra_Round
            INNER JOIN intra_Match on intra_Match.id = intra_Round.match_id
            WHERE intra_Match.kind = "simple" AND intra_Match.part1_id IS NOT NULL
                AND intra_Match.part1_id != intra_Match.part2_id
         ');

        $this->addSql('INSERT INTO
            intra_Score(
                round_id,
                participant_id,
                score
            )
            SELECT
                intra_Round.id as round_id,
                part2_id as participant_id,
                score2 as score
            FROM intra_Round
            INNER JOIN intra_Match on intra_Match.id = intra_Round.match_id
            WHERE intra_Match.kind = "simple" AND intra_Match.part2_id IS NOT NULL
                AND intra_Match.part1_id != intra_Match.part2_id
         ');

        $this->addSql('INSERT INTO
            intra_Score(
                round_id,
                participant_id,
                score
            )
            SELECT
                intra_Round.id as round_id,
                part1_id as participant_id,
                (score1 + score2) as score
            FROM intra_Round
            INNER JOIN intra_Match on intra_Match.id = intra_Round.match_id
            WHERE intra_Match.kind = "simple" AND intra_Match.part1_id IS NOT NULL AND intra_Match.part2_id IS NOT NULL
                AND intra_Match.part1_id = intra_Match.part2_id
         ');
        
        $this->addSql('ALTER TABLE intra_Round DROP score1, DROP score2');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('UPDATE intra_Round
            INNER JOIN intra_Match on intra_Match.id = intra_Round.match_id
            INNER JOIN intra_Score as Score1 on Score1.participant_id = intra_Match.part1_id and Score1.round_id = intra_Round.id
            INNER JOIN intra_Score as Score2 on Score2.participant_id = intra_Match.part2_id and Score2.round_id = intra_Round.id
            SET
                score1 = Score1.score,
                score2 = Score2.score
          ');

        $this->addSql('DROP TABLE intra_Score');
        $this->addSql('ALTER TABLE intra_Round ADD score1 INT NOT NULL, ADD score2 INT NOT NULL');
    }
}
