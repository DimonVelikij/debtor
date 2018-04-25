<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180425183020 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ownership_statuses (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, INDEX IDX_267B8EA7727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ownership_statuses ADD CONSTRAINT FK_267B8EA7727ACA70 FOREIGN KEY (parent_id) REFERENCES ownership_statuses (id)');
        $this->addSql('ALTER TABLE debtors ADD ownership_status_id INT NOT NULL');
        $this->addSql('ALTER TABLE debtors ADD CONSTRAINT FK_2A8D8D6849989D94 FOREIGN KEY (ownership_status_id) REFERENCES ownership_statuses (id)');
        $this->addSql('CREATE INDEX IDX_2A8D8D6849989D94 ON debtors (ownership_status_id)');

        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES(NULL, NULL, 'Собственник', 'owner')");
        $this->addSql("SET @OWNER_ID=LAST_INSERT_ID();");
        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES
        (NULL, @OWNER_ID, 'Единоличный собственник', 'owner_individual'), 
        (NULL, @OWNER_ID, 'Долевой собственник', 'owner_shared'), 
        (NULL, @OWNER_ID, 'Совместная собственность', 'owner_joint');");
        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES(NULL, NULL, 'Член семьи собственника', 'family_member_owner');");
        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES(NULL, NULL, 'Наниматель', 'employer');");
        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES(NULL, NULL, 'Член семьи нанимателя', 'family_member_employer');");
        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES(NULL, NULL, 'Законный представитель несовершеннолетнего собственника', 'legal_representative_underage_owner');");
        $this->addSql("SET @LEGAL_REPRESENTATIVE_UNDERAGE_OWNER_ID=LAST_INSERT_ID();");
        $this->addSql("INSERT INTO ownership_statuses (id, parent_id, title, alias) VALUES
        (NULL, @LEGAL_REPRESENTATIVE_UNDERAGE_OWNER_ID, 'Единоличный собственник', 'legal_representative_underage_owner_individual'),
        (NULL, @LEGAL_REPRESENTATIVE_UNDERAGE_OWNER_ID, 'Долевой собственник', 'legal_representative_underage_owner_shared'),
        (NULL, @LEGAL_REPRESENTATIVE_UNDERAGE_OWNER_ID, 'Совместная собственность', 'legal_representative_underage_owner_joint');");
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
        $this->addSql('DROP TABLE ownership_statuses');
        $this->addSql('DROP INDEX IDX_2A8D8D6849989D94 ON debtors');
        $this->addSql('ALTER TABLE debtors DROP ownership_status_id');
    }
}
