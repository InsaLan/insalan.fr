<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211204131003 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE lexik_maintenance');
        $this->addSql('ALTER TABLE intra_ArchivesPictureAlbum DROP FOREIGN KEY FK_665B43B574281A5E');
        $this->addSql('DROP INDEX idx_665b43b574281a5e ON intra_ArchivesPictureAlbum');
        $this->addSql('CREATE INDEX IDX_EBB88C8A74281A5E ON intra_ArchivesPictureAlbum (edition_id)');
        $this->addSql('ALTER TABLE intra_ArchivesPictureAlbum ADD CONSTRAINT FK_665B43B574281A5E FOREIGN KEY (edition_id) REFERENCES intra_ArchivesEdition (id)');
        $this->addSql('ALTER TABLE intra_ArchivesStream DROP FOREIGN KEY FK_3D06A97374281A5E');
        $this->addSql('DROP INDEX idx_3d06a97374281a5e ON intra_ArchivesStream');
        $this->addSql('CREATE INDEX IDX_66ED14574281A5E ON intra_ArchivesStream (edition_id)');
        $this->addSql('ALTER TABLE intra_ArchivesStream ADD CONSTRAINT FK_3D06A97374281A5E FOREIGN KEY (edition_id) REFERENCES intra_ArchivesEdition (id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder DROP FOREIGN KEY FK_B81400C58D9F6D38');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder DROP FOREIGN KEY FK_B81400C5A76ED395');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder DROP FOREIGN KEY FK_B81400C5D41D1D42');
        $this->addSql('DROP INDEX idx_b81400c5a76ed395 ON intra_PizzaUserOrder');
        $this->addSql('CREATE INDEX IDX_2DE02D49A76ED395 ON intra_PizzaUserOrder (user_id)');
        $this->addSql('DROP INDEX idx_b81400c58d9f6d38 ON intra_PizzaUserOrder');
        $this->addSql('CREATE INDEX IDX_2DE02D498D9F6D38 ON intra_PizzaUserOrder (order_id)');
        $this->addSql('DROP INDEX idx_b81400c5d41d1d42 ON intra_PizzaUserOrder');
        $this->addSql('CREATE INDEX IDX_2DE02D49D41D1D42 ON intra_PizzaUserOrder (pizza_id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder ADD CONSTRAINT FK_B81400C58D9F6D38 FOREIGN KEY (order_id) REFERENCES intra_PizzaOrder (id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder ADD CONSTRAINT FK_B81400C5A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder ADD CONSTRAINT FK_B81400C5D41D1D42 FOREIGN KEY (pizza_id) REFERENCES intra_Pizza (id)');
        $this->addSql('ALTER TABLE intra_player_tournamentteam DROP FOREIGN KEY FK_D5A91003296CD8AE');
        $this->addSql('ALTER TABLE intra_player_tournamentteam DROP FOREIGN KEY FK_D5A9100399E6F5DF');
        $this->addSql('DROP INDEX idx_d5a9100399e6f5df ON intra_player_tournamentteam');
        $this->addSql('CREATE INDEX IDX_2D95E7C399E6F5DF ON intra_player_tournamentteam (player_id)');
        $this->addSql('DROP INDEX idx_d5a91003296cd8ae ON intra_player_tournamentteam');
        $this->addSql('CREATE INDEX IDX_2D95E7C3B4F5C17D ON intra_player_tournamentteam (tournamentteam_id)');
        $this->addSql('ALTER TABLE intra_player_tournamentteam ADD CONSTRAINT FK_D5A91003296CD8AE FOREIGN KEY (tournamentteam_id) REFERENCES intra_TournamentTeam (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_player_tournamentteam ADD CONSTRAINT FK_D5A9100399E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament DROP FOREIGN KEY FK_2FD0E1B6F1FAD9D3');
        $this->addSql('DROP INDEX IDX_2FD0E1B6F1FAD9D3 ON intra_tournamentbundle_tournament');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament DROP FOREIGN KEY FK_2FD0E1B633D1A3E7');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament CHANGE bundle_id tournamentbundle_id INT NOT NULL');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament ADD CONSTRAINT FK_9CAECB343F847A56 FOREIGN KEY (tournamentbundle_id) REFERENCES intra_TournamentBundle (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9CAECB343F847A56 ON intra_tournamentbundle_tournament (tournamentbundle_id)');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament ADD PRIMARY KEY (tournamentbundle_id, tournament_id)');
        $this->addSql('DROP INDEX idx_2fd0e1b633d1a3e7 ON intra_tournamentbundle_tournament');
        $this->addSql('CREATE INDEX IDX_9CAECB3433D1A3E7 ON intra_tournamentbundle_tournament (tournament_id)');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament ADD CONSTRAINT FK_2FD0E1B633D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentGroup DROP FOREIGN KEY FK_763048092298D193');
        $this->addSql('DROP INDEX idx_763048092298d193 ON intra_TournamentGroup');
        $this->addSql('CREATE INDEX IDX_D8AC68A12298D193 ON intra_TournamentGroup (stage_id)');
        $this->addSql('ALTER TABLE intra_TournamentGroup ADD CONSTRAINT FK_763048092298D193 FOREIGN KEY (stage_id) REFERENCES intra_TournamentGroupStage (id)');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant DROP FOREIGN KEY FK_BDE911499D1C3019');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant DROP FOREIGN KEY FK_BDE91149FE54D947');
        $this->addSql('DROP INDEX idx_bde91149fe54d947 ON intra_tournamentgroup_participant');
        $this->addSql('CREATE INDEX IDX_E973BCBE113C330 ON intra_tournamentgroup_participant (tournamentgroup_id)');
        $this->addSql('DROP INDEX idx_bde911499d1c3019 ON intra_tournamentgroup_participant');
        $this->addSql('CREATE INDEX IDX_E973BCB9D1C3019 ON intra_tournamentgroup_participant (participant_id)');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant ADD CONSTRAINT FK_BDE911499D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant ADD CONSTRAINT FK_BDE91149FE54D947 FOREIGN KEY (tournamentgroup_id) REFERENCES intra_TournamentGroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentGroupStage DROP FOREIGN KEY FK_D03952E33D1A3E7');
        $this->addSql('DROP INDEX idx_d03952e33d1a3e7 ON intra_TournamentGroupStage');
        $this->addSql('CREATE INDEX IDX_29EB3EB433D1A3E7 ON intra_TournamentGroupStage (tournament_id)');
        $this->addSql('ALTER TABLE intra_TournamentGroupStage ADD CONSTRAINT FK_D03952E33D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockout DROP FOREIGN KEY FK_372BF28833D1A3E7');
        $this->addSql('DROP INDEX idx_372bf28833d1a3e7 ON intra_TournamentKnockout');
        $this->addSql('CREATE INDEX IDX_8C7D053333D1A3E7 ON intra_TournamentKnockout (tournament_id)');
        $this->addSql('ALTER TABLE intra_TournamentKnockout ADD CONSTRAINT FK_372BF28833D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_B27CC64B2ABEACD6');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_B27CC64B369E9CB8');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_B27CC64B727ACA70');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_B27CC64B7A6E108');
        $this->addSql('DROP INDEX idx_b27cc64b369e9cb8 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE INDEX IDX_A1DAF421369E9CB8 ON intra_TournamentKnockoutMatch (knockout_id)');
        $this->addSql('DROP INDEX uniq_b27cc64b2abeacd6 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A1DAF4212ABEACD6 ON intra_TournamentKnockoutMatch (match_id)');
        $this->addSql('DROP INDEX idx_b27cc64b727aca70 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE INDEX IDX_A1DAF421727ACA70 ON intra_TournamentKnockoutMatch (parent_id)');
        $this->addSql('DROP INDEX idx_b27cc64b7a6e108 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE INDEX IDX_A1DAF4217A6E108 ON intra_TournamentKnockoutMatch (loserDestination_id)');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_B27CC64B2ABEACD6 FOREIGN KEY (match_id) REFERENCES intra_TournamentMatch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_B27CC64B369E9CB8 FOREIGN KEY (knockout_id) REFERENCES intra_TournamentKnockout (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_B27CC64B727ACA70 FOREIGN KEY (parent_id) REFERENCES intra_TournamentKnockoutMatch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_B27CC64B7A6E108 FOREIGN KEY (loserDestination_id) REFERENCES intra_TournamentKnockoutMatch (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_94DDEA2033D1A3E7');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_94DDEA209D1C3019');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_94DDEA20A76ED395');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_94DDEA20C0BC769D');
        $this->addSql('DROP INDEX idx_94ddea20a76ed395 ON intra_TournamentManager');
        $this->addSql('CREATE INDEX IDX_C96D5295A76ED395 ON intra_TournamentManager (user_id)');
        $this->addSql('DROP INDEX idx_94ddea2033d1a3e7 ON intra_TournamentManager');
        $this->addSql('CREATE INDEX IDX_C96D529533D1A3E7 ON intra_TournamentManager (tournament_id)');
        $this->addSql('DROP INDEX uniq_94ddea209d1c3019 ON intra_TournamentManager');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C96D52959D1C3019 ON intra_TournamentManager (participant_id)');
        $this->addSql('DROP INDEX uniq_94ddea20c0bc769d ON intra_TournamentManager');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C96D5295C0BC769D ON intra_TournamentManager (eTicket_id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_94DDEA2033D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_94DDEA209D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_94DDEA20A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_94DDEA20C0BC769D FOREIGN KEY (eTicket_id) REFERENCES intra_e_ticket (id)');
        $this->addSql('ALTER TABLE intra_TournamentMatch DROP FOREIGN KEY FK_61ABC9C96D29CF65');
        $this->addSql('ALTER TABLE intra_TournamentMatch DROP FOREIGN KEY FK_61ABC9C97F9C608B');
        $this->addSql('ALTER TABLE intra_TournamentMatch DROP FOREIGN KEY FK_61ABC9C9FE54D947');
        $this->addSql('DROP INDEX idx_61abc9c9fe54d947 ON intra_TournamentMatch');
        $this->addSql('CREATE INDEX IDX_CF37E961FE54D947 ON intra_TournamentMatch (group_id)');
        $this->addSql('DROP INDEX idx_61abc9c97f9c608b ON intra_TournamentMatch');
        $this->addSql('CREATE INDEX IDX_CF37E9617F9C608B ON intra_TournamentMatch (part1_id)');
        $this->addSql('DROP INDEX idx_61abc9c96d29cf65 ON intra_TournamentMatch');
        $this->addSql('CREATE INDEX IDX_CF37E9616D29CF65 ON intra_TournamentMatch (part2_id)');
        $this->addSql('ALTER TABLE intra_TournamentMatch ADD CONSTRAINT FK_61ABC9C96D29CF65 FOREIGN KEY (part2_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentMatch ADD CONSTRAINT FK_61ABC9C97F9C608B FOREIGN KEY (part1_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentMatch ADD CONSTRAINT FK_61ABC9C9FE54D947 FOREIGN KEY (group_id) REFERENCES intra_TournamentGroup (id)');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant DROP FOREIGN KEY FK_1EA892549D1C3019');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant DROP FOREIGN KEY FK_1EA89254C19B7E03');
        $this->addSql('DROP INDEX idx_1ea89254c19b7e03 ON tournamentroyalmatch_participant');
        $this->addSql('CREATE INDEX IDX_66A7255FD4260FC ON tournamentroyalmatch_participant (tournamentroyalmatch_id)');
        $this->addSql('DROP INDEX idx_1ea892549d1c3019 ON tournamentroyalmatch_participant');
        $this->addSql('CREATE INDEX IDX_66A7255F9D1C3019 ON tournamentroyalmatch_participant (participant_id)');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant ADD CONSTRAINT FK_1EA892549D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant ADD CONSTRAINT FK_1EA89254C19B7E03 FOREIGN KEY (tournamentroyalmatch_id) REFERENCES intra_TournamentMatch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentRound DROP FOREIGN KEY FK_DE1EE6F82ABEACD6');
        $this->addSql('DROP INDEX idx_de1ee6f82abeacd6 ON intra_TournamentRound');
        $this->addSql('CREATE INDEX IDX_7082C6502ABEACD6 ON intra_TournamentRound (match_id)');
        $this->addSql('ALTER TABLE intra_TournamentRound ADD CONSTRAINT FK_DE1EE6F82ABEACD6 FOREIGN KEY (match_id) REFERENCES intra_TournamentMatch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentScore DROP FOREIGN KEY FK_29693B9D9D1C3019');
        $this->addSql('ALTER TABLE intra_TournamentScore DROP FOREIGN KEY FK_29693B9DA6005CA0');
        $this->addSql('DROP INDEX idx_29693b9da6005ca0 ON intra_TournamentScore');
        $this->addSql('CREATE INDEX IDX_87F51B35A6005CA0 ON intra_TournamentScore (round_id)');
        $this->addSql('DROP INDEX idx_29693b9d9d1c3019 ON intra_TournamentScore');
        $this->addSql('CREATE INDEX IDX_87F51B359D1C3019 ON intra_TournamentScore (participant_id)');
        $this->addSql('ALTER TABLE intra_TournamentScore ADD CONSTRAINT FK_29693B9D9D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentScore ADD CONSTRAINT FK_29693B9DA6005CA0 FOREIGN KEY (round_id) REFERENCES intra_TournamentRound (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentTeam DROP FOREIGN KEY FK_3549114A3346729B');
        $this->addSql('DROP INDEX uniq_3549114a3346729b ON intra_TournamentTeam');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7803F3A23346729B ON intra_TournamentTeam (captain_id)');
        $this->addSql('ALTER TABLE intra_TournamentTeam ADD CONSTRAINT FK_3549114A3346729B FOREIGN KEY (captain_id) REFERENCES intra_Player (id)');
        $this->addSql('ALTER TABLE intra_UserDiscount DROP FOREIGN KEY FK_685D662141B618A8');
        $this->addSql('DROP INDEX idx_685d662141b618a8 ON intra_UserDiscount');
        $this->addSql('CREATE INDEX IDX_87809C6841B618A8 ON intra_UserDiscount (registrable_id)');
        $this->addSql('ALTER TABLE intra_UserDiscount ADD CONSTRAINT FK_685D662141B618A8 FOREIGN KEY (registrable_id) REFERENCES intra_Registrable (id)');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder DROP FOREIGN KEY FK_C0C5840A4C3A3BB');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder DROP FOREIGN KEY FK_C0C5840A6796D554');
        $this->addSql('DROP INDEX idx_c0c5840a6796d554 ON intra_UserMerchantOrder');
        $this->addSql('CREATE INDEX IDX_204A01B16796D554 ON intra_UserMerchantOrder (merchant_id)');
        $this->addSql('DROP INDEX idx_c0c5840a4c3a3bb ON intra_UserMerchantOrder');
        $this->addSql('CREATE INDEX IDX_204A01B14C3A3BB ON intra_UserMerchantOrder (payment_id)');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder ADD CONSTRAINT FK_C0C5840A4C3A3BB FOREIGN KEY (payment_id) REFERENCES intra_payum_payment_details (id)');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder ADD CONSTRAINT FK_C0C5840A6796D554 FOREIGN KEY (merchant_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player DROP FOREIGN KEY FK_150E963820E9F9DE');
        $this->addSql('DROP INDEX IDX_150E963820E9F9DE ON intra_usermerchantorder_player');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player DROP FOREIGN KEY FK_150E963899E6F5DF');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player CHANGE merchantorder_id usermerchantorder_id INT NOT NULL');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player ADD CONSTRAINT FK_621E90084FEE72BD FOREIGN KEY (usermerchantorder_id) REFERENCES intra_UserMerchantOrder (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_621E90084FEE72BD ON intra_usermerchantorder_player (usermerchantorder_id)');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player ADD PRIMARY KEY (usermerchantorder_id, player_id)');
        $this->addSql('DROP INDEX idx_150e963899e6f5df ON intra_usermerchantorder_player');
        $this->addSql('CREATE INDEX IDX_621E900899E6F5DF ON intra_usermerchantorder_player (player_id)');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player ADD CONSTRAINT FK_150E963899E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_UserPaymentToken ADD id INT NOT NULL, CHANGE hash hash VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE intra_ext_log_entries CHANGE object_class object_class VARCHAR(191) NOT NULL, CHANGE username username VARCHAR(191) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE lexik_maintenance (ttl DATETIME DEFAULT NULL) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE intra_ArchivesPictureAlbum DROP FOREIGN KEY FK_EBB88C8A74281A5E');
        $this->addSql('DROP INDEX idx_ebb88c8a74281a5e ON intra_ArchivesPictureAlbum');
        $this->addSql('CREATE INDEX IDX_665B43B574281A5E ON intra_ArchivesPictureAlbum (edition_id)');
        $this->addSql('ALTER TABLE intra_ArchivesPictureAlbum ADD CONSTRAINT FK_EBB88C8A74281A5E FOREIGN KEY (edition_id) REFERENCES intra_ArchivesEdition (id)');
        $this->addSql('ALTER TABLE intra_ArchivesStream DROP FOREIGN KEY FK_66ED14574281A5E');
        $this->addSql('DROP INDEX idx_66ed14574281a5e ON intra_ArchivesStream');
        $this->addSql('CREATE INDEX IDX_3D06A97374281A5E ON intra_ArchivesStream (edition_id)');
        $this->addSql('ALTER TABLE intra_ArchivesStream ADD CONSTRAINT FK_66ED14574281A5E FOREIGN KEY (edition_id) REFERENCES intra_ArchivesEdition (id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder DROP FOREIGN KEY FK_2DE02D49A76ED395');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder DROP FOREIGN KEY FK_2DE02D498D9F6D38');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder DROP FOREIGN KEY FK_2DE02D49D41D1D42');
        $this->addSql('DROP INDEX idx_2de02d49a76ed395 ON intra_PizzaUserOrder');
        $this->addSql('CREATE INDEX IDX_B81400C5A76ED395 ON intra_PizzaUserOrder (user_id)');
        $this->addSql('DROP INDEX idx_2de02d498d9f6d38 ON intra_PizzaUserOrder');
        $this->addSql('CREATE INDEX IDX_B81400C58D9F6D38 ON intra_PizzaUserOrder (order_id)');
        $this->addSql('DROP INDEX idx_2de02d49d41d1d42 ON intra_PizzaUserOrder');
        $this->addSql('CREATE INDEX IDX_B81400C5D41D1D42 ON intra_PizzaUserOrder (pizza_id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder ADD CONSTRAINT FK_2DE02D49A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder ADD CONSTRAINT FK_2DE02D498D9F6D38 FOREIGN KEY (order_id) REFERENCES intra_PizzaOrder (id)');
        $this->addSql('ALTER TABLE intra_PizzaUserOrder ADD CONSTRAINT FK_2DE02D49D41D1D42 FOREIGN KEY (pizza_id) REFERENCES intra_Pizza (id)');
        $this->addSql('ALTER TABLE intra_TournamentGroup DROP FOREIGN KEY FK_D8AC68A12298D193');
        $this->addSql('DROP INDEX idx_d8ac68a12298d193 ON intra_TournamentGroup');
        $this->addSql('CREATE INDEX IDX_763048092298D193 ON intra_TournamentGroup (stage_id)');
        $this->addSql('ALTER TABLE intra_TournamentGroup ADD CONSTRAINT FK_D8AC68A12298D193 FOREIGN KEY (stage_id) REFERENCES intra_TournamentGroupStage (id)');
        $this->addSql('ALTER TABLE intra_TournamentGroupStage DROP FOREIGN KEY FK_29EB3EB433D1A3E7');
        $this->addSql('DROP INDEX idx_29eb3eb433d1a3e7 ON intra_TournamentGroupStage');
        $this->addSql('CREATE INDEX IDX_D03952E33D1A3E7 ON intra_TournamentGroupStage (tournament_id)');
        $this->addSql('ALTER TABLE intra_TournamentGroupStage ADD CONSTRAINT FK_29EB3EB433D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockout DROP FOREIGN KEY FK_8C7D053333D1A3E7');
        $this->addSql('DROP INDEX idx_8c7d053333d1a3e7 ON intra_TournamentKnockout');
        $this->addSql('CREATE INDEX IDX_372BF28833D1A3E7 ON intra_TournamentKnockout (tournament_id)');
        $this->addSql('ALTER TABLE intra_TournamentKnockout ADD CONSTRAINT FK_8C7D053333D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_A1DAF421369E9CB8');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_A1DAF4212ABEACD6');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_A1DAF421727ACA70');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch DROP FOREIGN KEY FK_A1DAF4217A6E108');
        $this->addSql('DROP INDEX idx_a1daf4217a6e108 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE INDEX IDX_B27CC64B7A6E108 ON intra_TournamentKnockoutMatch (loserDestination_id)');
        $this->addSql('DROP INDEX uniq_a1daf4212abeacd6 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B27CC64B2ABEACD6 ON intra_TournamentKnockoutMatch (match_id)');
        $this->addSql('DROP INDEX idx_a1daf421369e9cb8 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE INDEX IDX_B27CC64B369E9CB8 ON intra_TournamentKnockoutMatch (knockout_id)');
        $this->addSql('DROP INDEX idx_a1daf421727aca70 ON intra_TournamentKnockoutMatch');
        $this->addSql('CREATE INDEX IDX_B27CC64B727ACA70 ON intra_TournamentKnockoutMatch (parent_id)');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_A1DAF421369E9CB8 FOREIGN KEY (knockout_id) REFERENCES intra_TournamentKnockout (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_A1DAF4212ABEACD6 FOREIGN KEY (match_id) REFERENCES `intra_TournamentMatch` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_A1DAF421727ACA70 FOREIGN KEY (parent_id) REFERENCES intra_TournamentKnockoutMatch (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentKnockoutMatch ADD CONSTRAINT FK_A1DAF4217A6E108 FOREIGN KEY (loserDestination_id) REFERENCES intra_TournamentKnockoutMatch (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_C96D5295A76ED395');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_C96D529533D1A3E7');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_C96D52959D1C3019');
        $this->addSql('ALTER TABLE intra_TournamentManager DROP FOREIGN KEY FK_C96D5295C0BC769D');
        $this->addSql('DROP INDEX idx_c96d529533d1a3e7 ON intra_TournamentManager');
        $this->addSql('CREATE INDEX IDX_94DDEA2033D1A3E7 ON intra_TournamentManager (tournament_id)');
        $this->addSql('DROP INDEX uniq_c96d52959d1c3019 ON intra_TournamentManager');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94DDEA209D1C3019 ON intra_TournamentManager (participant_id)');
        $this->addSql('DROP INDEX uniq_c96d5295c0bc769d ON intra_TournamentManager');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_94DDEA20C0BC769D ON intra_TournamentManager (eTicket_id)');
        $this->addSql('DROP INDEX idx_c96d5295a76ed395 ON intra_TournamentManager');
        $this->addSql('CREATE INDEX IDX_94DDEA20A76ED395 ON intra_TournamentManager (user_id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_C96D5295A76ED395 FOREIGN KEY (user_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_C96D529533D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_C96D52959D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id)');
        $this->addSql('ALTER TABLE intra_TournamentManager ADD CONSTRAINT FK_C96D5295C0BC769D FOREIGN KEY (eTicket_id) REFERENCES intra_e_ticket (id)');
        $this->addSql('ALTER TABLE `intra_TournamentMatch` DROP FOREIGN KEY FK_CF37E961FE54D947');
        $this->addSql('ALTER TABLE `intra_TournamentMatch` DROP FOREIGN KEY FK_CF37E9617F9C608B');
        $this->addSql('ALTER TABLE `intra_TournamentMatch` DROP FOREIGN KEY FK_CF37E9616D29CF65');
        $this->addSql('DROP INDEX idx_cf37e961fe54d947 ON `intra_TournamentMatch`');
        $this->addSql('CREATE INDEX IDX_61ABC9C9FE54D947 ON `intra_TournamentMatch` (group_id)');
        $this->addSql('DROP INDEX idx_cf37e9617f9c608b ON `intra_TournamentMatch`');
        $this->addSql('CREATE INDEX IDX_61ABC9C97F9C608B ON `intra_TournamentMatch` (part1_id)');
        $this->addSql('DROP INDEX idx_cf37e9616d29cf65 ON `intra_TournamentMatch`');
        $this->addSql('CREATE INDEX IDX_61ABC9C96D29CF65 ON `intra_TournamentMatch` (part2_id)');
        $this->addSql('ALTER TABLE `intra_TournamentMatch` ADD CONSTRAINT FK_CF37E961FE54D947 FOREIGN KEY (group_id) REFERENCES intra_TournamentGroup (id)');
        $this->addSql('ALTER TABLE `intra_TournamentMatch` ADD CONSTRAINT FK_CF37E9617F9C608B FOREIGN KEY (part1_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE `intra_TournamentMatch` ADD CONSTRAINT FK_CF37E9616D29CF65 FOREIGN KEY (part2_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentRound DROP FOREIGN KEY FK_7082C6502ABEACD6');
        $this->addSql('DROP INDEX idx_7082c6502abeacd6 ON intra_TournamentRound');
        $this->addSql('CREATE INDEX IDX_DE1EE6F82ABEACD6 ON intra_TournamentRound (match_id)');
        $this->addSql('ALTER TABLE intra_TournamentRound ADD CONSTRAINT FK_7082C6502ABEACD6 FOREIGN KEY (match_id) REFERENCES `intra_TournamentMatch` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentScore DROP FOREIGN KEY FK_87F51B35A6005CA0');
        $this->addSql('ALTER TABLE intra_TournamentScore DROP FOREIGN KEY FK_87F51B359D1C3019');
        $this->addSql('DROP INDEX idx_87f51b35a6005ca0 ON intra_TournamentScore');
        $this->addSql('CREATE INDEX IDX_29693B9DA6005CA0 ON intra_TournamentScore (round_id)');
        $this->addSql('DROP INDEX idx_87f51b359d1c3019 ON intra_TournamentScore');
        $this->addSql('CREATE INDEX IDX_29693B9D9D1C3019 ON intra_TournamentScore (participant_id)');
        $this->addSql('ALTER TABLE intra_TournamentScore ADD CONSTRAINT FK_87F51B35A6005CA0 FOREIGN KEY (round_id) REFERENCES intra_TournamentRound (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentScore ADD CONSTRAINT FK_87F51B359D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_TournamentTeam DROP FOREIGN KEY FK_7803F3A23346729B');
        $this->addSql('DROP INDEX uniq_7803f3a23346729b ON intra_TournamentTeam');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3549114A3346729B ON intra_TournamentTeam (captain_id)');
        $this->addSql('ALTER TABLE intra_TournamentTeam ADD CONSTRAINT FK_7803F3A23346729B FOREIGN KEY (captain_id) REFERENCES intra_Player (id)');
        $this->addSql('ALTER TABLE intra_UserDiscount DROP FOREIGN KEY FK_87809C6841B618A8');
        $this->addSql('DROP INDEX idx_87809c6841b618a8 ON intra_UserDiscount');
        $this->addSql('CREATE INDEX IDX_685D662141B618A8 ON intra_UserDiscount (registrable_id)');
        $this->addSql('ALTER TABLE intra_UserDiscount ADD CONSTRAINT FK_87809C6841B618A8 FOREIGN KEY (registrable_id) REFERENCES intra_Registrable (id)');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder DROP FOREIGN KEY FK_204A01B16796D554');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder DROP FOREIGN KEY FK_204A01B14C3A3BB');
        $this->addSql('DROP INDEX idx_204a01b16796d554 ON intra_UserMerchantOrder');
        $this->addSql('CREATE INDEX IDX_C0C5840A6796D554 ON intra_UserMerchantOrder (merchant_id)');
        $this->addSql('DROP INDEX idx_204a01b14c3a3bb ON intra_UserMerchantOrder');
        $this->addSql('CREATE INDEX IDX_C0C5840A4C3A3BB ON intra_UserMerchantOrder (payment_id)');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder ADD CONSTRAINT FK_204A01B16796D554 FOREIGN KEY (merchant_id) REFERENCES intra_User (id)');
        $this->addSql('ALTER TABLE intra_UserMerchantOrder ADD CONSTRAINT FK_204A01B14C3A3BB FOREIGN KEY (payment_id) REFERENCES intra_payum_payment_details (id)');
        $this->addSql('ALTER TABLE intra_UserPaymentToken DROP id, CHANGE hash hash VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE intra_ext_log_entries CHANGE object_class object_class VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE username username VARCHAR(255) CHARACTER SET utf8 DEFAULT NULL COLLATE `utf8_unicode_ci`');
        $this->addSql('ALTER TABLE intra_player_tournamentteam DROP FOREIGN KEY FK_2D95E7C399E6F5DF');
        $this->addSql('ALTER TABLE intra_player_tournamentteam DROP FOREIGN KEY FK_2D95E7C3B4F5C17D');
        $this->addSql('DROP INDEX idx_2d95e7c399e6f5df ON intra_player_tournamentteam');
        $this->addSql('CREATE INDEX IDX_D5A9100399E6F5DF ON intra_player_tournamentteam (player_id)');
        $this->addSql('DROP INDEX idx_2d95e7c3b4f5c17d ON intra_player_tournamentteam');
        $this->addSql('CREATE INDEX IDX_D5A91003296CD8AE ON intra_player_tournamentteam (tournamentteam_id)');
        $this->addSql('ALTER TABLE intra_player_tournamentteam ADD CONSTRAINT FK_2D95E7C399E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_player_tournamentteam ADD CONSTRAINT FK_2D95E7C3B4F5C17D FOREIGN KEY (tournamentteam_id) REFERENCES intra_TournamentTeam (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament DROP FOREIGN KEY FK_9CAECB343F847A56');
        $this->addSql('DROP INDEX IDX_9CAECB343F847A56 ON intra_tournamentbundle_tournament');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament DROP FOREIGN KEY FK_9CAECB3433D1A3E7');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament CHANGE tournamentbundle_id bundle_id INT NOT NULL');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament ADD CONSTRAINT FK_2FD0E1B6F1FAD9D3 FOREIGN KEY (bundle_id) REFERENCES intra_TournamentBundle (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2FD0E1B6F1FAD9D3 ON intra_tournamentbundle_tournament (bundle_id)');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament ADD PRIMARY KEY (bundle_id, tournament_id)');
        $this->addSql('DROP INDEX idx_9caecb3433d1a3e7 ON intra_tournamentbundle_tournament');
        $this->addSql('CREATE INDEX IDX_2FD0E1B633D1A3E7 ON intra_tournamentbundle_tournament (tournament_id)');
        $this->addSql('ALTER TABLE intra_tournamentbundle_tournament ADD CONSTRAINT FK_9CAECB3433D1A3E7 FOREIGN KEY (tournament_id) REFERENCES intra_Tournament (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant DROP FOREIGN KEY FK_E973BCBE113C330');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant DROP FOREIGN KEY FK_E973BCB9D1C3019');
        $this->addSql('DROP INDEX idx_e973bcbe113c330 ON intra_tournamentgroup_participant');
        $this->addSql('CREATE INDEX IDX_BDE91149FE54D947 ON intra_tournamentgroup_participant (tournamentgroup_id)');
        $this->addSql('DROP INDEX idx_e973bcb9d1c3019 ON intra_tournamentgroup_participant');
        $this->addSql('CREATE INDEX IDX_BDE911499D1C3019 ON intra_tournamentgroup_participant (participant_id)');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant ADD CONSTRAINT FK_E973BCBE113C330 FOREIGN KEY (tournamentgroup_id) REFERENCES intra_TournamentGroup (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_tournamentgroup_participant ADD CONSTRAINT FK_E973BCB9D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player DROP FOREIGN KEY FK_621E90084FEE72BD');
        $this->addSql('DROP INDEX IDX_621E90084FEE72BD ON intra_usermerchantorder_player');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player DROP FOREIGN KEY FK_621E900899E6F5DF');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player CHANGE usermerchantorder_id merchantorder_id INT NOT NULL');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player ADD CONSTRAINT FK_150E963820E9F9DE FOREIGN KEY (merchantorder_id) REFERENCES intra_UserMerchantOrder (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_150E963820E9F9DE ON intra_usermerchantorder_player (merchantorder_id)');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player ADD PRIMARY KEY (merchantorder_id, player_id)');
        $this->addSql('DROP INDEX idx_621e900899e6f5df ON intra_usermerchantorder_player');
        $this->addSql('CREATE INDEX IDX_150E963899E6F5DF ON intra_usermerchantorder_player (player_id)');
        $this->addSql('ALTER TABLE intra_usermerchantorder_player ADD CONSTRAINT FK_621E900899E6F5DF FOREIGN KEY (player_id) REFERENCES intra_Player (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant DROP FOREIGN KEY FK_66A7255FD4260FC');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant DROP FOREIGN KEY FK_66A7255F9D1C3019');
        $this->addSql('DROP INDEX idx_66a7255fd4260fc ON tournamentroyalmatch_participant');
        $this->addSql('CREATE INDEX IDX_1EA89254C19B7E03 ON tournamentroyalmatch_participant (tournamentroyalmatch_id)');
        $this->addSql('DROP INDEX idx_66a7255f9d1c3019 ON tournamentroyalmatch_participant');
        $this->addSql('CREATE INDEX IDX_1EA892549D1C3019 ON tournamentroyalmatch_participant (participant_id)');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant ADD CONSTRAINT FK_66A7255FD4260FC FOREIGN KEY (tournamentroyalmatch_id) REFERENCES `intra_TournamentMatch` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tournamentroyalmatch_participant ADD CONSTRAINT FK_66A7255F9D1C3019 FOREIGN KEY (participant_id) REFERENCES intra_Participant (id) ON DELETE CASCADE');
    }
}
