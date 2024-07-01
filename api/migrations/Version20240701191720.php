<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701191720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification_return ADD user_notification_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN notification_return.user_notification_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE notification_return ADD CONSTRAINT FK_6339C152FDC6F10B FOREIGN KEY (user_notification_id) REFERENCES user_notification (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6339C152FDC6F10B ON notification_return (user_notification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE notification_return DROP CONSTRAINT FK_6339C152FDC6F10B');
        $this->addSql('DROP INDEX IDX_6339C152FDC6F10B');
        $this->addSql('ALTER TABLE notification_return DROP user_notification_id');
    }
}
