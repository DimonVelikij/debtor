<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20181101153611 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE houses_judicial_sectors (house_id INT NOT NULL, judicial_sector_id INT NOT NULL, INDEX IDX_646211536BB74515 (house_id), INDEX IDX_646211533B132C31 (judicial_sector_id), PRIMARY KEY(house_id, judicial_sector_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE houses_judicial_sectors ADD CONSTRAINT FK_646211536BB74515 FOREIGN KEY (house_id) REFERENCES houses (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE houses_judicial_sectors ADD CONSTRAINT FK_646211533B132C31 FOREIGN KEY (judicial_sector_id) REFERENCES judicial_sectors (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE judicial_sectors ADD type SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB3B132C31');
        $this->addSql('DROP INDEX IDX_95D7F5CB3B132C31 ON houses');
        $this->addSql('ALTER TABLE houses DROP judicial_sector_id');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE houses_judicial_sectors');
        $this->addSql('ALTER TABLE houses ADD judicial_sector_id INT NOT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB3B132C31 FOREIGN KEY (judicial_sector_id) REFERENCES judicial_sectors (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB3B132C31 ON houses (judicial_sector_id)');
        $this->addSql('ALTER TABLE judicial_sectors DROP type');
    }
}
