<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240805060636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add optout column to subscription table and set it to true for existing records';
    }

    public function up(Schema $schema): void
    {
        // Add the optout column to the subscription table with a default value
        $this->addSql('ALTER TABLE subscription ADD optout BOOLEAN DEFAULT TRUE');
        
        // Ensure existing records have optout set to true
        $this->addSql('UPDATE subscription SET optout = TRUE');
        
        // Alter the optout column to remove the default value
        $this->addSql('ALTER TABLE subscription ALTER COLUMN optout SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the optout column from the subscription table
        $this->addSql('ALTER TABLE subscription DROP optout');
    }
}
