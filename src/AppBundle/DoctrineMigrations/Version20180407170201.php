<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180407170201 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
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
        $this->addSql("DELETE FROM ownership_statuses");
    }
}
