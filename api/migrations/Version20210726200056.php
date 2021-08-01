<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210726200056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE ipgreturn_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE ipgreturn (id INT NOT NULL, transaction_id INT DEFAULT NULL, txndate TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, approval_code VARCHAR(255) NOT NULL, notification_hash VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, endpoint_transaction_id INT DEFAULT NULL, ipg_transaction_id INT DEFAULT NULL, currency INT DEFAULT NULL, total DOUBLE PRECISION DEFAULT NULL, fail_reason VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E2D45E062FC0CB0F ON ipgreturn (transaction_id)');
        $this->addSql('ALTER TABLE ipgreturn ADD CONSTRAINT FK_E2D45E062FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE ipgreturn_id_seq CASCADE');
        $this->addSql('DROP TABLE ipgreturn');
    }
}
