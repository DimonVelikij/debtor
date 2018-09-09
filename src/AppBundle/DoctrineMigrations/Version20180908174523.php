<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180908174523 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE personal_accounts ADD date_open_account DATE NOT NULL, ADD date_close_account DATE DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_66F740E57D3656A4 ON personal_accounts (account)');
        $this->addSql('ALTER TABLE subscribers DROP INDEX UNIQ_2FCD16ACDBBBF81A, ADD INDEX IDX_2FCD16ACDBBBF81A (personal_account_id)');
        $this->addSql('ALTER TABLE subscribers DROP date_open_account, DROP date_close_account');
        $this->addSql('ALTER TABLE debtors DROP INDEX UNIQ_2A8D8D68DBBBF81A, ADD INDEX IDX_2A8D8D68DBBBF81A (personal_account_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE debtors DROP INDEX IDX_2A8D8D68DBBBF81A, ADD UNIQUE INDEX UNIQ_2A8D8D68DBBBF81A (personal_account_id)');
        $this->addSql('DROP INDEX UNIQ_66F740E57D3656A4 ON personal_accounts');
        $this->addSql('ALTER TABLE personal_accounts DROP date_open_account, DROP date_close_account');
        $this->addSql('ALTER TABLE subscribers DROP INDEX IDX_2FCD16ACDBBBF81A, ADD UNIQUE INDEX UNIQ_2FCD16ACDBBBF81A (personal_account_id)');
        $this->addSql('ALTER TABLE subscribers ADD date_open_account DATE NOT NULL, ADD date_close_account DATE DEFAULT NULL');
    }
}
