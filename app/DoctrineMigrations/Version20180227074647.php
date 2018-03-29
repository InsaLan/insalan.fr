<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180227074647 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE intra_Edition (id INT AUTO_INCREMENT NOT NULL, year INT NOT NULL, image VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intra_Picture (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intra_Stream (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $editions = array(
                array('year' => 2008, 'image' => 'InsaLan_3'),
                array('year' => 2009, 'image' => 'InsaLan_4'),
                array('year' => 2010, 'image' => 'InsaLan_5'),
                array('year' => 2011, 'image' => 'InsaLan_6'),
                array('year' => 2012, 'image' => 'InsaLan_7'),
                array('year' => 2013, 'image' => 'InsaLan_8'),
                array('year' => 2014, 'image' => 'InsaLan_9'),
                array('year' => 2015, 'image' => 'InsaLan_10'),
                array('year' => 2016, 'image' => 'InsaLan_11'),
                array('year' => 2017, 'image' => 'InsaLan_12'),
                array('year' => 2018, 'image' => 'InsaLan_13'),
                );
        foreach ($editions as $edition) {
            $this->addSql('INSERT INTO `intra_edition` (`id`, `year`, `image`) VALUES (NULL, :year, :image)', $edition);
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');


        $this->addSql('DROP TABLE intra_Edition');
        $this->addSql('DROP TABLE intra_Picture');
        $this->addSql('DROP TABLE intra_Stream');
    }
}
