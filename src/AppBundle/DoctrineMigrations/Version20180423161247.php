<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180423161247 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats CHANGE date_fill_debt date_fill_debt DATE NOT NULL, CHANGE sum_debt sum_debt DOUBLE PRECISION NOT NULL, CHANGE period_accrued_debt period_accrued_debt DOUBLE PRECISION DEFAULT NULL, CHANGE period_pay_debt period_pay_debt DOUBLE PRECISION DEFAULT NULL, CHANGE period_accrued_fine period_accrued_fine DOUBLE PRECISION DEFAULT NULL, CHANGE period_pay_fine period_pay_fine DOUBLE PRECISION DEFAULT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE flats CHANGE date_fill_debt date_fill_debt DATE DEFAULT NULL, CHANGE sum_debt sum_debt DOUBLE PRECISION DEFAULT NULL, CHANGE period_accrued_debt period_accrued_debt DOUBLE PRECISION NOT NULL, CHANGE period_pay_debt period_pay_debt DOUBLE PRECISION NOT NULL, CHANGE period_accrued_fine period_accrued_fine DOUBLE PRECISION NOT NULL, CHANGE period_pay_fine period_pay_fine DOUBLE PRECISION NOT NULL');
    }
}
