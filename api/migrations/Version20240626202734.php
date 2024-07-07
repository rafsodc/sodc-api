<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626202734 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notify_message_user DROP CONSTRAINT fk_921ff7b7ae6d9765');
        $this->addSql('CREATE TABLE bulk_notification (id UUID NOT NULL, roles TEXT NOT NULL, data JSON NOT NULL, template_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN bulk_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN bulk_notification.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN bulk_notification.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_notification (id UUID NOT NULL, owner_id INT NOT NULL, bulk_notification_id UUID DEFAULT NULL, sent BOOLEAN NOT NULL, status VARCHAR(255) DEFAULT NULL, data JSON NOT NULL, template_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3F980AC87E3C61F9 ON user_notification (owner_id)');
        $this->addSql('CREATE INDEX IDX_3F980AC8EF7CF2E3 ON user_notification (bulk_notification_id)');
        $this->addSql('COMMENT ON COLUMN user_notification.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_notification.bulk_notification_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_notification.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC87E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8EF7CF2E3 FOREIGN KEY (bulk_notification_id) REFERENCES bulk_notification (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE notify_message');
        $this->addSql('DROP TABLE notify_message_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT FK_3F980AC8EF7CF2E3');
        $this->addSql('CREATE TABLE notify_message (id UUID NOT NULL, roles TEXT NOT NULL, data JSON NOT NULL, template_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notify_message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notify_message.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN notify_message.template_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE notify_message_user (id UUID NOT NULL, owner_id INT NOT NULL, notify_message_id UUID NOT NULL, sent BOOLEAN NOT NULL, status VARCHAR(255) DEFAULT NULL, data JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_921ff7b77e3c61f9 ON notify_message_user (owner_id)');
        $this->addSql('CREATE INDEX idx_921ff7b7ae6d9765 ON notify_message_user (notify_message_id)');
        $this->addSql('COMMENT ON COLUMN notify_message_user.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notify_message_user.notify_message_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notify_message_user ADD CONSTRAINT fk_921ff7b77e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE notify_message_user ADD CONSTRAINT fk_921ff7b7ae6d9765 FOREIGN KEY (notify_message_id) REFERENCES notify_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE bulk_notification');
        $this->addSql('DROP TABLE user_notification');
    }
}
