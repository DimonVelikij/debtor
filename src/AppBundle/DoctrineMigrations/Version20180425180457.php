<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180425180457 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE debtors (id INT AUTO_INCREMENT NOT NULL, flat_id INT NOT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, start_date_ownership DATE DEFAULT NULL, end_date_ownership DATE DEFAULT NULL, archive TINYINT(1) DEFAULT NULL, subscriber TINYINT(1) DEFAULT NULL, share_size VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, place_of_birth VARCHAR(255) DEFAULT NULL, owner_name VARCHAR(255) DEFAULT NULL, ogrnip VARCHAR(255) DEFAULT NULL, inn VARCHAR(255) DEFAULT NULL, ogrn VARCHAR(255) DEFAULT NULL, boss_name VARCHAR(255) DEFAULT NULL, boss_position VARCHAR(255) DEFAULT NULL, INDEX IDX_2A8D8D68D3331C94 (flat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68D3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE debtors');
    }
}
