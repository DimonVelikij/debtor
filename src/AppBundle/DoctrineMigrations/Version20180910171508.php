<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180910171508 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CBC60E870D');
        $this->addSql('DROP TABLE mkds');
        $this->addSql('DROP INDEX UNIQ_95D7F5CBC60E870D ON houses');
        $this->addSql('ALTER TABLE houses ADD fssp_department_id INT NOT NULL, ADD management_start_date DATE NOT NULL, ADD management_end_date DATE DEFAULT NULL, ADD legal_document_name VARCHAR(255) NOT NULL, ADD legal_document_date DATE NOT NULL, ADD legal_document_number VARCHAR(255) DEFAULT NULL, CHANGE mkd_id judicial_sector_id INT NOT NULL');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB3B132C31 FOREIGN KEY (judicial_sector_id) REFERENCES judicial_sectors (id)');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CB991B3F1E FOREIGN KEY (fssp_department_id) REFERENCES fssp_departments (id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB3B132C31 ON houses (judicial_sector_id)');
        $this->addSql('CREATE INDEX IDX_95D7F5CB991B3F1E ON houses (fssp_department_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE mkds (id INT AUTO_INCREMENT NOT NULL, judicial_sector_id INT NOT NULL, street_id INT NOT NULL, fssp_department_id INT NOT NULL, house_number VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, management_start_date DATE NOT NULL, management_end_date DATE DEFAULT NULL, legal_document_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, legal_document_date DATE NOT NULL, legal_document_number VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, INDEX IDX_64175A2687CF8EB (street_id), INDEX IDX_64175A263B132C31 (judicial_sector_id), INDEX IDX_64175A26991B3F1E (fssp_department_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mkds ADD CONSTRAINT FK_64175A263B132C31 FOREIGN KEY (judicial_sector_id) REFERENCES judicial_sectors (id)');
        $this->addSql('ALTER TABLE mkds ADD CONSTRAINT FK_64175A2687CF8EB FOREIGN KEY (street_id) REFERENCES streets (id)');
        $this->addSql('ALTER TABLE mkds ADD CONSTRAINT FK_64175A26991B3F1E FOREIGN KEY (fssp_department_id) REFERENCES fssp_departments (id)');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB3B132C31');
        $this->addSql('ALTER TABLE houses DROP FOREIGN KEY FK_95D7F5CB991B3F1E');
        $this->addSql('DROP INDEX IDX_95D7F5CB3B132C31 ON houses');
        $this->addSql('DROP INDEX IDX_95D7F5CB991B3F1E ON houses');
        $this->addSql('ALTER TABLE houses ADD mkd_id INT NOT NULL, DROP judicial_sector_id, DROP fssp_department_id, DROP management_start_date, DROP management_end_date, DROP legal_document_name, DROP legal_document_date, DROP legal_document_number');
        $this->addSql('ALTER TABLE houses ADD CONSTRAINT FK_95D7F5CBC60E870D FOREIGN KEY (mkd_id) REFERENCES mkds (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_95D7F5CBC60E870D ON houses (mkd_id)');
    }
}
