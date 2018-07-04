<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180702193351 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mkds (id INT AUTO_INCREMENT NOT NULL, street_id INT NOT NULL, judicial_sector_id INT NOT NULL, fssp_department_id INT NOT NULL, house_number VARCHAR(255) NOT NULL, management_start_date DATE NOT NULL, management_end_date DATE DEFAULT NULL, legal_document_name VARCHAR(255) NOT NULL, legal_document_date DATE NOT NULL, legal_document_number VARCHAR(255) DEFAULT NULL, INDEX IDX_64175A2687CF8EB (street_id), INDEX IDX_64175A263B132C31 (judicial_sector_id), INDEX IDX_64175A26991B3F1E (fssp_department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mkds ADD CONSTRAINT FK_64175A2687CF8EB FOREIGN KEY (street_id) REFERENCES streets (id)');
        $this->addSql('ALTER TABLE mkds ADD CONSTRAINT FK_64175A263B132C31 FOREIGN KEY (judicial_sector_id) REFERENCES judicial_sectors (id)');
        $this->addSql('ALTER TABLE mkds ADD CONSTRAINT FK_64175A26991B3F1E FOREIGN KEY (fssp_department_id) REFERENCES fssp_departments (id)');
        $this->addSql('ALTER TABLE cities ADD `index` VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE houses ADD mkd_id INT NOT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBC60E870D FOREIGN KEY (mkd_id) REFERENCES mkds (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_95D7F5CBC60E870D ON houses (mkd_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBC60E870D');
        $this->addSql('DROP TABLE mkds');
        $this->addSql('ALTER TABLE cities DROP `index`');
        $this->addSql('DROP INDEX UNIQ_95D7F5CBC60E870D ON houses');
        $this->addSql('ALTER TABLE houses DROP mkd_id');
    }
}
