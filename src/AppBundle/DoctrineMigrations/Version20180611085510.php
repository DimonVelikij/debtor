<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180611085510 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats_events DROP FOREIGN KEY FK_60190F2B71F7E88B');
        $this->addSql('ALTER TABLE flats_events ADD date_generate DATETIME NOT NULL, ADD data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2B71F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flats DROP event_data');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats ADD event_data LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE flats_events DROP FOREIGN KEY FK_60190F2B71F7E88B');
        $this->addSql('ALTER TABLE flats_events DROP date_generate, DROP data');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2B71F7E88B FOREIGN KEY (event_id) REFERENCES events (id)');
    }
}
