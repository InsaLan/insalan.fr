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
        $this->addSql('CREATE TABLE intra_PictureArchives (id INT AUTO_INCREMENT NOT NULL, path VARCHAR(255) NOT NULL, date DATETIME NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE intra_StreamArchives (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        $editions = array(
                array('year' => 2008, 'image' => 'InsaLan_3.jpg'),
                array('year' => 2009, 'image' => 'InsaLan_4.png'),
                array('year' => 2010, 'image' => 'InsaLan_5.png'),
                array('year' => 2011, 'image' => 'InsaLan_6.png'),
                array('year' => 2012, 'image' => 'InsaLan_7.png'),
                array('year' => 2013, 'image' => 'InsaLan_8.png'),
                array('year' => 2014, 'image' => 'InsaLan_9.jpg'),
                array('year' => 2015, 'image' => 'InsaLan_10.jpg'),
                array('year' => 2016, 'image' => 'InsaLan_11.jpg'),
                array('year' => 2017, 'image' => 'InsaLan_12.jpg'),
                array('year' => 2018, 'image' => 'InsaLan_13.jpg'),
                array('year' => 2019, 'image' => 'InsaLan_14.jpg'),
                );
        foreach ($editions as $edition) {
            $this->addSql('INSERT INTO `intra_Edition` (`id`, `year`, `image`) VALUES (NULL, :year, :image)', $edition);
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
        $this->addSql('DROP TABLE intra_PictureArchives');
        $this->addSql('DROP TABLE intra_StreamArchives');
    }
}
