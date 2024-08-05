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
        return 'Modify bulk_notification table to add subscription_id and set it using an existing subscription value.';
    }

    public function up(Schema $schema): void
    {
        // Add the subscription_id column to the bulk_notification table allowing null values initially
        $this->addSql('ALTER TABLE bulk_notification ADD subscription_id UUID DEFAULT NULL');

        // Retrieve the specific subscription_id from the subscription table
        $subscriptionId = $this->connection->fetchOne('SELECT uuid FROM subscription LIMIT 1');

        // Update the bulk_notification table with this subscription_id
        $this->addSql('UPDATE bulk_notification SET subscription_id = ?', [$subscriptionId]);

        // Alter the subscription_id column to disallow null values
        $this->addSql('ALTER TABLE bulk_notification ALTER COLUMN subscription_id SET NOT NULL');

        // Drop the roles and is_mailing columns
        $this->addSql('ALTER TABLE bulk_notification DROP roles');
        $this->addSql('ALTER TABLE bulk_notification DROP is_mailing');

        // Add a comment to the subscription_id column
        $this->addSql('COMMENT ON COLUMN bulk_notification.subscription_id IS \'(DC2Type:uuid)\'');

        // Add foreign key constraint and index to the subscription_id column
        $this->addSql('ALTER TABLE bulk_notification ADD CONSTRAINT FK_E55C52599A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E55C52599A1887DC ON bulk_notification (subscription_id)');

        // Alter the subscription table to drop the default value for the optout column
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

