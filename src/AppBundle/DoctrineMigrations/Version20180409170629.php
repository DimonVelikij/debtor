<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180409170629 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ownership_statuses ADD CONSTRAINT FK_267B8EA7727ACA70 FOREIGN KEY (parent_id) REFERENCES ownership_statuses (id)');
        $this->addSql('DROP INDEX IDX_2A8D8D685EE5FD36 ON debtors');
        $this->addSql('ALTER TABLE debtors CHANGE debtor_status_id debtor_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68F71BC577 FOREIGN KEY (debtor_type_id) REFERENCES debtor_types (id)');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D6849989D94 FOREIGN KEY (ownership_status_id) REFERENCES ownership_statuses (id)');
        $this->addSql('CREATE INDEX IDX_2A8D8D68F71BC577 ON debtors (debtor_type_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D68F71BC577');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D6849989D94');
        $this->addSql('DROP INDEX IDX_2A8D8D68F71BC577 ON debtors');
        $this->addSql('ALTER TABLE debtors CHANGE debtor_type_id debtor_status_id INT NOT NULL');
        $this->addSql('CREATE INDEX IDX_2A8D8D685EE5FD36 ON debtors (debtor_status_id)');
        $this->addSql('ALTER TABLE ownership_statuses DROP FOREIGN KEY FK_267B8EA7727ACA70');
    }
}
