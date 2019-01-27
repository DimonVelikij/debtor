<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20190126152817 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE personal_accounts_events (personal_account_id INT NOT NULL, event_id INT NOT NULL, date_generate DATETIME NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\', INDEX IDX_102D1787DBBBF81A (personal_account_id), INDEX IDX_102D178771F7E88B (event_id), PRIMARY KEY(personal_account_id, event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_history (id INT AUTO_INCREMENT NOT NULL, personal_account_id INT NOT NULL, date DATE NOT NULL, debt DOUBLE PRECISION NOT NULL, fine DOUBLE PRECISION NOT NULL, INDEX IDX_3EF37EA1DBBBF81A (personal_account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE personal_accounts_events ADD CONSTRAINT FK_102D1787DBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE personal_accounts_events ADD CONSTRAINT FK_102D178771F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE payment_history ADD CONSTRAINT FK_3EF37EA1DBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE flats_events');
        $this->addSql('ALTER TABLE flats DROP start_debt_period, DROP end_debt_period, DROP date_fill_debt, DROP sum_debt, DROP period_accrued_debt, DROP period_pay_debt, DROP date_fill_fine, DROP sum_fine, DROP period_accrued_fine, DROP period_pay_fine, DROP updated_at, DROP is_generate_errors, DROP event_data');
        $this->addSql('ALTER TABLE personal_accounts ADD flat_id INT NOT NULL, ADD generate_errors VARCHAR(255) DEFAULT NULL, ADD event_data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE personal_accounts ADD CONSTRAINT FK_66F740E5D3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_66F740E5D3331C94 ON personal_accounts (flat_id)');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D68D3331C94');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D68DBBBF81A');
        $this->addSql('DROP INDEX IDX_2A8D8D68D3331C94 ON debtors');
        $this->addSql('ALTER TABLE debtors DROP flat_id');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68DBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscribers DROP FOREIGN KEY FK_2FCD16ACD3331C94');
        $this->addSql('ALTER TABLE subscribers DROP FOREIGN KEY FK_2FCD16ACDBBBF81A');
        $this->addSql('DROP INDEX IDX_2FCD16ACD3331C94 ON subscribers');
        $this->addSql('ALTER TABLE subscribers DROP flat_id');
        $this->addSql('ALTER TABLE subscribers ADD CONSTRAINT FK_2FCD16ACDBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CD3331C94');
        $this->addSql('DROP INDEX IDX_F08FC65CD3331C94 ON logs');
        $this->addSql('ALTER TABLE logs DROP is_read, DROP event, CHANGE flat_id personal_account_id INT NOT NULL');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CDBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F08FC65CDBBBF81A ON logs (personal_account_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flats_events (flat_id INT NOT NULL, event_id INT NOT NULL, date_generate DATETIME NOT NULL, data LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:object)\', INDEX IDX_60190F2BD3331C94 (flat_id), INDEX IDX_60190F2B71F7E88B (event_id), PRIMARY KEY(flat_id, event_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2B71F7E88B FOREIGN KEY (event_id) REFERENCES events (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE flats_events ADD CONSTRAINT FK_60190F2BD3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE personal_accounts_events');
        $this->addSql('DROP TABLE payment_history');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D68DBBBF81A');
        $this->addSql('ALTER TABLE debtors ADD flat_id INT NOT NULL');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68D3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68DBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id)');
        $this->addSql('CREATE INDEX IDX_2A8D8D68D3331C94 ON debtors (flat_id)');
        $this->addSql('ALTER TABLE flats ADD start_debt_period DATE DEFAULT NULL, ADD end_debt_period DATE DEFAULT NULL, ADD date_fill_debt DATE DEFAULT NULL, ADD sum_debt DOUBLE PRECISION NOT NULL, ADD period_accrued_debt DOUBLE PRECISION DEFAULT NULL, ADD period_pay_debt DOUBLE PRECISION DEFAULT NULL, ADD date_fill_fine DATE DEFAULT NULL, ADD sum_fine DOUBLE PRECISION DEFAULT NULL, ADD period_accrued_fine DOUBLE PRECISION DEFAULT NULL, ADD period_pay_fine DOUBLE PRECISION DEFAULT NULL, ADD updated_at DATETIME NOT NULL, ADD is_generate_errors TINYINT(1) DEFAULT \'0\' NOT NULL, ADD event_data LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:object)\'');
        $this->addSql('ALTER TABLE logs DROP FOREIGN KEY FK_F08FC65CDBBBF81A');
        $this->addSql('DROP INDEX IDX_F08FC65CDBBBF81A ON logs');
        $this->addSql('ALTER TABLE logs ADD is_read TINYINT(1) NOT NULL, ADD event VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE personal_account_id flat_id INT NOT NULL');
        $this->addSql('ALTER TABLE logs ADD CONSTRAINT FK_F08FC65CD3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_F08FC65CD3331C94 ON logs (flat_id)');
        $this->addSql('ALTER TABLE personal_accounts DROP FOREIGN KEY FK_66F740E5D3331C94');
        $this->addSql('DROP INDEX IDX_66F740E5D3331C94 ON personal_accounts');
        $this->addSql('ALTER TABLE personal_accounts DROP flat_id, DROP generate_errors, DROP event_data');
        $this->addSql('ALTER TABLE subscribers DROP FOREIGN KEY FK_2FCD16ACDBBBF81A');
        $this->addSql('ALTER TABLE subscribers ADD flat_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscribers ADD CONSTRAINT FK_2FCD16ACD3331C94 FOREIGN KEY (flat_id) REFERENCES flats (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subscribers ADD CONSTRAINT FK_2FCD16ACDBBBF81A FOREIGN KEY (personal_account_id) REFERENCES personal_accounts (id)');
        $this->addSql('CREATE INDEX IDX_2FCD16ACD3331C94 ON subscribers (flat_id)');
    }
}
