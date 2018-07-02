<?php

namespace AppBundle\DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180625165401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Должник поступил в работу', 'entered_processing', NULL, NULL, 'pretense')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Претензия №1', 'pretense1', NULL, NULL, 'pretense')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Претензия №2', 'pretense2', NULL, NULL, 'pretense')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Формирование заявления на выдачу судебного приказа', 'formation_court_order', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Подача заявления на выдачу судебного приказа', 'applying_court_order', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Получение судебного приказа', 'obtaining_court_order', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Формирование искового заявления', 'formation_statement_claim', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Подача искового заявления', 'applying_statement_claim', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Проверка принятия дела к производству', 'verification_case', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Судебное делопроизводство', 'legal_proceedings', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Заявление на получение исполнительного листа', 'statement_receipt_writ_execution', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Получение исполнительного листа', 'obtaining_writ_execution', NULL, NULL, 'judicature')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Заявление на возбуждение исполнительного производства', 'statement_commencement_enforcement_proceedings', NULL, NULL, 'performance')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Подача заявления на возбуждение исполнительного производства', 'submission_commencement_enforcement_proceedings', NULL, NULL, 'performance')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Контроль исполнительного производства', 'control_enforcement_proceedings', NULL, NULL, 'performance')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Подготовка заявления на бездействие ФССП', 'statement_preparation_FSSP', NULL, NULL, 'performance')");
        $this->addSql("INSERT INTO events (id, name, alias, template, template_fields, type) VALUES (NULL, 'Подача заявления на бездействие ФССП', 'applying_statement_FSSP', NULL, NULL, 'performance')");

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->addSql("DELETE FROM events");

    }
}
