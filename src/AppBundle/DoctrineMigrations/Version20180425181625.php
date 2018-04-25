<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180425181625 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE debtor_types (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE debtors ADD type_id INT NOT NULL');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68C54C8C93 FOREIGN KEY (type_id) REFERENCES debtor_types (id)');
        $this->addSql('CREATE INDEX IDX_2A8D8D68C54C8C93 ON debtors (type_id)');
        $this->addSql("INSERT INTO debtor_types (id, title, alias) VALUES (NULL, 'Физическое лицо', 'individual'), (NULL, 'Индивидульный предприниматель', 'businessman'), (NULL, 'Юридическое лицо', 'legal')");
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D68C54C8C93');
        $this->addSql('DROP TABLE debtor_types');
        $this->addSql('DROP INDEX IDX_2A8D8D68C54C8C93 ON debtors');
        $this->addSql('ALTER TABLE debtors DROP type_id');
    }
}
