<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240623183412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE notify_message_id_seq CASCADE');
        $this->addSql('CREATE TABLE notify_message_user (id UUID NOT NULL, owner_id INT NOT NULL, notify_message_id UUID NOT NULL, sent BOOLEAN NOT NULL, status VARCHAR(255) DEFAULT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_921FF7B77E3C61F9 ON notify_message_user (owner_id)');
        $this->addSql('CREATE INDEX IDX_921FF7B7AE6D9765 ON notify_message_user (notify_message_id)');
        $this->addSql('COMMENT ON COLUMN notify_message_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notify_message_user.notify_message_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notify_message_user ADD CONSTRAINT FK_921FF7B77E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notify_message_user ADD CONSTRAINT FK_921FF7B7AE6D9765 FOREIGN KEY (notify_message_id) REFERENCES notify_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE notify_message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE notify_message_user');
    }
}
