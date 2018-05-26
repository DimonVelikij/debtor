<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180526064255 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE templates (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, template LONGTEXT DEFAULT NULL, time_perform_action INT NOT NULL, template_fields LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', is_start TINYINT(1) DEFAULT \'0\' NOT NULL, is_judicial TINYINT(1) DEFAULT \'0\' NOT NULL, UNIQUE INDEX UNIQ_6F287D8E989D9B62 (slug), UNIQUE INDEX UNIQ_6F287D8E727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE templates ADD CONSTRAINT FK_6F287D8E727ACA70 FOREIGN KEY (parent_id) REFERENCES templates (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE templates DROP FOREIGN KEY FK_6F287D8E727ACA70');
        $this->addSql('DROP TABLE templates');
    }
}
