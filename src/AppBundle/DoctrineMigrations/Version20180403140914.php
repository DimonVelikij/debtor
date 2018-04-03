<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180403140914 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ownership_statuses (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, INDEX IDX_267B8EA7727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE debtor_types (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE debtors (id INT AUTO_INCREMENT NOT NULL, company_id INT NOT NULL, debtor_status_id INT NOT NULL, ownership_status_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, start_date_ownership DATE DEFAULT NULL, end_date_ownership DATE DEFAULT NULL, start_debt_period DATE DEFAULT NULL, end_debt_period DATE DEFAULT NULL, date_fill_debt DATE DEFAULT NULL, sum_debt DOUBLE PRECISION DEFAULT NULL, period_accrued_debt DOUBLE PRECISION NOT NULL, period_pay_debt DOUBLE PRECISION NOT NULL, date_fill_fine DATE DEFAULT NULL, sum_fine DOUBLE PRECISION DEFAULT NULL, period_accrued_fine DOUBLE PRECISION NOT NULL, period_pay_fine DOUBLE PRECISION NOT NULL, arhive TINYINT(1) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, place_of_birth VARCHAR(255) DEFAULT NULL, owner_name VARCHAR(255) DEFAULT NULL, ogrnip VARCHAR(255) DEFAULT NULL, inn VARCHAR(255) DEFAULT NULL, ogrn VARCHAR(255) DEFAULT NULL, boss_name VARCHAR(255) DEFAULT NULL, boss_position VARCHAR(255) DEFAULT NULL, INDEX IDX_2A8D8D68979B1AD6 (company_id), INDEX IDX_2A8D8D685EE5FD36 (debtor_status_id), INDEX IDX_2A8D8D6849989D94 (ownership_status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ownership_statuses ADD CONSTRAINT FK_267B8EA7727ACA70 FOREIGN KEY (parent_id) REFERENCES ownership_statuses (id)');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D68979B1AD6 FOREIGN KEY (company_id) REFERENCES companies (id)');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D685EE5FD36 FOREIGN KEY (debtor_status_id) REFERENCES debtor_types (id)');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D6849989D94 FOREIGN KEY (ownership_status_id) REFERENCES ownership_statuses (id)');
        $this->addSql('INSERT INTO debtor_types (id, title, alias) VALUES(NULL, "Физическое лицо", "individual"), (NULL, "Индивидульный предприниматель", "businessman"), (NULL, "Юридическое лицо", "legal_entity")');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ownership_statuses DROP FOREIGN KEY FK_267B8EA7727ACA70');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D6849989D94');
        $this->addSql('ALTER TABLE debtors DROP FOREIGN KEY FK_2A8D8D685EE5FD36');
        $this->addSql('DROP TABLE ownership_statuses');
        $this->addSql('DROP TABLE debtor_types');
        $this->addSql('DROP TABLE debtors');
    }
}
