<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180912165123 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flat_types (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE street_types (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE streets ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE streets ADD CONSTRAINT FK_93F67B3EC54C8C93 FOREIGN KEY (type_id) REFERENCES street_types (id)');
        $this->addSql('CREATE INDEX IDX_93F67B3EC54C8C93 ON streets (type_id)');
        $this->addSql('ALTER TABLE flats ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE flats ADD CONSTRAINT FK_6AEA0028C54C8C93 FOREIGN KEY (type_id) REFERENCES flat_types (id)');
        $this->addSql('CREATE INDEX IDX_6AEA0028C54C8C93 ON flats (type_id)');
        $this->addSql('INSERT INTO street_types (id, title) VALUES (NULL, "улица"), (NULL, "переулок"), (NULL, "проезд"), (NULL, "тракт")');
        $this->addSql('INSERT INTO flat_types (id, title) VALUES (NULL, "квартира"), (NULL, "комната"), (NULL, "офис"), (NULL, "помещение")');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats DROP FOREIGN KEY FK_6AEA0028C54C8C93');
        $this->addSql('ALTER TABLE streets DROP FOREIGN KEY FK_93F67B3EC54C8C93');
        $this->addSql('DROP TABLE flat_types');
        $this->addSql('DROP TABLE street_types');
        $this->addSql('DROP INDEX IDX_6AEA0028C54C8C93 ON flats');
        $this->addSql('ALTER TABLE flats DROP type_id');
        $this->addSql('DROP INDEX IDX_93F67B3EC54C8C93 ON streets');
        $this->addSql('ALTER TABLE streets DROP type_id');
    }
}
