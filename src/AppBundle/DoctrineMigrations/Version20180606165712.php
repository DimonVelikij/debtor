<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180606165712 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats_events DROP FOREIGN KEY FK_60190F2BD3331C94');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2BD3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats_events DROP FOREIGN KEY FK_60190F2BD3331C94');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2BD3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id)');
    }
}
