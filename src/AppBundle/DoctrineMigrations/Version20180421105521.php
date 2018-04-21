<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180421105521 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE flats (id INT AUTO_INCREMENT NOT NULL, house_id INT NOT NULL, number VARCHAR(255) DEFAULT NULL, start_debt_period DATE DEFAULT NULL, end_debt_period DATE DEFAULT NULL, date_fill_debt DATE DEFAULT NULL, sum_debt DOUBLE PRECISION DEFAULT NULL, period_accrued_debt DOUBLE PRECISION NOT NULL, period_pay_debt DOUBLE PRECISION NOT NULL, date_fill_fine DATE DEFAULT NULL, sum_fine DOUBLE PRECISION DEFAULT NULL, period_accrued_fine DOUBLE PRECISION NOT NULL, period_pay_fine DOUBLE PRECISION NOT NULL, arhive TINYINT(1) DEFAULT NULL, INDEX IDX_6AEA00286BB74515 (house_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flats ADD CONSTRAINT FK_6AEA00286BB74515 FOREIGN KEY (house_id) REFERENCES houses (id) ON DELETE CASCADE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE flats');
    }
}
