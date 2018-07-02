<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180625165313 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE events DROP FOREIGN KEY FK_5387574A727ACA70');
        $this->addSql('DROP INDEX UNIQ_5387574A989D9B62 ON events');
        $this->addSql('DROP INDEX UNIQ_5387574A727ACA70 ON events');
        $this->addSql('ALTER TABLE events ADD type ENUM(\'pretense\', \'judicature\', \'performance\'), DROP parent_id, DROP time_perform_action, DROP is_start, DROP is_judicial, CHANGE slug alias VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5387574AE16C6B94 ON events (alias)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_5387574AE16C6B94 ON events');
        $this->addSql('ALTER TABLE events ADD parent_id INT DEFAULT NULL, ADD time_perform_action INT NOT NULL, ADD slug VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, ADD is_start TINYINT(1) DEFAULT \'0\', ADD is_judicial TINYINT(1) DEFAULT \'0\', DROP alias, DROP type');
        $this->addSql('ALTER TABLE events ADD CONSTRAINT FK_5387574A727ACA70 FOREIGN KEY (parent_id) REFERENCES events (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5387574A989D9B62 ON events (slug)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5387574A727ACA70 ON events (parent_id)');
    }
}
