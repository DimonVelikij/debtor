<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180605175250 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats DROP FOREIGN KEY FK_6AEA00285DA0FB8');
        $this->addSql('ALTER TABLE templates DROP FOREIGN KEY FK_6F287D8E727ACA70');
        $this->addSql('CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, template LONGTEXT DEFAULT NULL, time_perform_action INT NOT NULL, template_fields LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', is_start TINYINT(1) DEFAULT \'0\', is_judicial TINYINT(1) DEFAULT \'0\', UNIQUE INDEX UNIQ_5387574A989D9B62 (slug), UNIQUE INDEX UNIQ_5387574A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE flats_events (flat_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_60190F2BD3331C94 (flat_id), INDEX IDX_60190F2B71F7E88B (event_id), PRIMARY KEY(flat_id, event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A727ACA70 FOREIGN KEY (parent_id) REFERENCES events (id)');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2BD3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id)');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2B71F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
        $this->addSql('DROP TABLE templates');
        $this->addSql('DROP INDEX IDX_6AEA00285DA0FB8 ON flats');
        $this->addSql('ALTER TABLE flats ADD event_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', DROP template_id, DROP last_date_generate');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A727ACA70');
        $this->addSql('ALTER TABLE flats_events DROP FOREIGN KEY FK_60190F2B71F7E88B');
        $this->addSql('CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, template LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, time_perform_action INT NOT NULL, template_fields LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:object)\', is_start TINYINT(1) DEFAULT \'0\', is_judicial TINYINT(1) DEFAULT \'0\', UNIQUE INDEX UNIQ_6F287D8E989D9B62 (slug), UNIQUE INDEX UNIQ_6F287D8E727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT FK_6F287D8E727ACA70 FOREIGN KEY (parent_id) REFERENCES templates (id)');
        $this->addSql('DROP TABLE events');
        $this->addSql('DROP TABLE flats_events');
        $this->addSql('ALTER TABLE flats ADD template_id INT NOT NULL, ADD last_date_generate DATE NOT NULL, DROP event_data');
        $this->addSql('ALTER TABLE flats ADD CONSTRAINT FK_6AEA00285DA0FB8 FOREIGN KEY (template_id) REFERENCES templates (id)');
        $this->addSql('CREATE INDEX IDX_6AEA00285DA0FB8 ON flats (template_id)');
    }
}
