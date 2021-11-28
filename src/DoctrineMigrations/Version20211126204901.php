<?php

declare(strict_types=1);

namespace App\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211126204901 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE global_vars (id INT AUTO_INCREMENT NOT NULL, staff_number INT NOT NULL, number INT NOT NULL, letters_number VARCHAR(255) NOT NULL, roman_number VARCHAR(255) NOT NULL, players_number INT NOT NULL, opening_date VARCHAR(255) NOT NULL, opening_hour VARCHAR(255) NOT NULL, closing_date VARCHAR(255) NOT NULL, closing_hour VARCHAR(255) NOT NULL, price INT NOT NULL, web_price INT NOT NULL, campanile_price INT NOT NULL, cosplay_edition INT NOT NULL, cosplay_name VARCHAR(255) NOT NULL, cosplay_date VARCHAR(255) NOT NULL, cosplay_end_registration VARCHAR(255) NOT NULL, full_dates VARCHAR(255) NOT NULL, pay_check_address VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE global_vars');
    }
}
