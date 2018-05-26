<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180526064944 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats ADD template_id INT NOT NULL');
        $this->addSql('ALTER TABLE flats ADD CONSTRAINT FK_6AEA00285DA0FB8 FOREIGN KEY (template_id) REFERENCES templates (id)');
        $this->addSql('CREATE INDEX IDX_6AEA00285DA0FB8 ON flats (template_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats DROP FOREIGN KEY FK_6AEA00285DA0FB8');
        $this->addSql('DROP INDEX IDX_6AEA00285DA0FB8 ON flats');
        $this->addSql('ALTER TABLE flats DROP template_id');
    }
}
