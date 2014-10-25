<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141025165859 extends AbstractMigration
{   

    /**
     * Amazing migration function
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        // Clean old schema

        $this->addSql('ALTER TABLE intra_User DROP FOREIGN KEY FK_7C3A611C99E6F5DF');
        $this->addSql('ALTER TABLE intra_Player DROP FOREIGN KEY FK_AD004F2296CD8AE');
        $this->addSql('ALTER TABLE intra_Group DROP FOREIGN KEY FK_763048092298D193');

        $this->addSql('DROP INDEX UNIQ_AD004F23D66DA87 ON intra_Player');
        $this->addSql('DROP INDEX IDX_AD004F2296CD8AE ON intra_Player');
        $this->addSql('DROP TABLE intra_GroupMatch');
        $this->addSql('DROP INDEX UNIQ_7C3A611C99E6F5DF ON intra_User');

        // Create MANY-TO-MANY : player <=> team

        $this->addSql('CREATE TABLE intra_player_team (player_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_D5A9100399E6F5DF (player_id), INDEX IDX_D5A91003296CD8AE (team_id), PRIMARY KEY(player_id, team_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // Update DATA, very important !
        
        /**
         * Many-to-One ===> Many-to-Many
         *
         * Player        player_team
         * ------        -----------
         * id         => player_id
         * team_id    => team_id
         */
        $this->addSql("INSERT INTO intra_player_team (player_id, team_id)
                       SELECT p.id, p.team_id FROM intra_Player p WHERE p.team_id IS NOT NULL");


        /**
         * Player : team_id becomes user_id
         * User   : player_id disappears
         *
         * User  (join) Player
         * ----         ------
         * id         = user_id
         * player_id  = id
         */
        $this->addSQL("UPDATE intra_Player p LEFT JOIN intra_User u ON p.id = u.player_id SET p.team_id = u.id");
        
        // Update schema

        $this->addSql('ALTER TABLE intra_User DROP player_id');
        $this->addSql('ALTER TABLE intra_Participant CHANGE validated validated INT NOT NULL');
        $this->addSql('ALTER TABLE intra_Player CHANGE team_id user_id INT DEFAULT NULL, CHANGE name lolName VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE intra_Tournament ADD registrationOpen DATE NOT NULL, ADD registrationClose DATE NOT NULL, ADD registrationLimit INT NOT NULL, ADD type enum(\'lol\', \'dota2\', \'sc2\', \'hs\', \'csgo\', \'manual\'), ADD participantType enum(\'team\', \'player\'), ADD teamMinPlayer INT NOT NULL, ADD teamMaxPlayer INT NOT NULL, ADD logoPath VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE intra_Match ADD group_id INT DEFAULT NULL');
        
        // Adding constraints
        
        $this->addSql('ALTER TABLE intra_player_team ADD CONSTRAINT FK_D5A9100399E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_player_team ADD CONSTRAINT FK_D5A91003296CD8AE FOREIGN KEY (team_id) REFERENCES intra_Team (id) ON DELETE CASCADE');

        $this->addSql('ALTER TABLE intra_Player ADD CONSTRAINT FK_AD004F2A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AD004F2A76ED395 ON intra_Player (user_id)');

        $this->addSql('ALTER TABLE intra_Match ADD CONSTRAINT FK_61ABC9C9FE54D947 FOREIGN KEY (group_id) REFERENCES `intra_Group` (id)');
        $this->addSql('CREATE INDEX IDX_61ABC9C9FE54D947 ON intra_Match (group_id)');
        
        $this->addSql('ALTER TABLE intra_Group ADD CONSTRAINT FK_763048092298D193 FOREIGN KEY (stage_id) REFERENCES intra_GroupStage (id)');
        
        $this->addSql('ALTER TABLE intra_KnockoutMatch DROP INDEX IDX_B27CC64B2ABEACD6, ADD UNIQUE INDEX UNIQ_B27CC64B2ABEACD6 (match_id)');

        // Last changes
        
        $this->addSql('UPDATE intra_Participant SET tournament_id =1');

    }

    public function down(Schema $schema)
    {
        $this->abortIf(true, 'Down migration is not possible, sorry.');
    }
}
