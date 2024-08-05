<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240805063846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Delete all rows from bulk_notification table
        $this->addSql('DELETE FROM user_notification');
        $this->addSql('DELETE FROM bulk_notification');

        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bulk_notification ADD subscription_id UUID NOT NULL');
        $this->addSql('ALTER TABLE bulk_notification DROP roles');
        $this->addSql('ALTER TABLE bulk_notification DROP is_mailing');
        $this->addSql('COMMENT ON COLUMN bulk_notification.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE bulk_notification ADD CONSTRAINT FK_E55C52599A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E55C52599A1887DC ON bulk_notification (subscription_id)');
        $this->addSql('ALTER TABLE subscription ALTER optout DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE bulk_notification DROP CONSTRAINT FK_E55C52599A1887DC');
        $this->addSql('DROP INDEX IDX_E55C52599A1887DC');
        $this->addSql('ALTER TABLE bulk_notification ADD roles TEXT NOT NULL');
        $this->addSql('ALTER TABLE bulk_notification ADD is_mailing BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE bulk_notification DROP subscription_id');
        $this->addSql('COMMENT ON COLUMN bulk_notification.roles IS \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE subscription ALTER optout SET DEFAULT true');
    }
}
