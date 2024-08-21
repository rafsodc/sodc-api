<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240821054934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles column to subscription table, set default roles to ["ROLE_MEMBER"], and then enforce NOT NULL constraint';
    }

    public function up(Schema $schema): void
    {
        // Add the roles column to the subscription table without NOT NULL constraint
        $this->addSql('ALTER TABLE subscription ADD roles JSON DEFAULT \'[]\'');

        // Set the default value of the roles column to ["ROLE_MEMBER"] for all existing subscriptions
        $this->addSql('UPDATE subscription SET roles = \'["ROLE_MEMBER"]\'');

        // Alter the roles column to enforce NOT NULL constraint
        $this->addSql('ALTER TABLE subscription ALTER COLUMN roles SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Drop the roles column from the subscription table
        $this->addSql('ALTER TABLE subscription DROP roles');
    }
}
