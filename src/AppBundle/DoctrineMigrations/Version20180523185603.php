<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180523185603 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE personal_accounts (id INT AUTO_INCREMENT NOT NULL, account VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subscribers ADD personal_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscribers ADD CONSTRAINT FK_2FCD16ACDBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FCD16ACDBBBF81A ON subscribers (personal_account_id)');
        $this->addSql('ALTER TABLE debtors ADD personal_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68DBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A8D8D68DBBBF81A ON debtors (personal_account_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE subscribers DROP FOREIGN KEY FK_2FCD16ACDBBBF81A');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D68DBBBF81A');
        $this->addSql('DROP TABLE personal_accounts');
        $this->addSql('DROP INDEX UNIQ_2A8D8D68DBBBF81A ON debtors');
        $this->addSql('ALTER TABLE debtors DROP personal_account_id');
        $this->addSql('DROP INDEX UNIQ_2FCD16ACDBBBF81A ON subscribers');
        $this->addSql('ALTER TABLE subscribers DROP personal_account_id');
    }
}
