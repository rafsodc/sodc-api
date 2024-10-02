<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241002164604 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add cancelled column to ticket table, set default value to false, update existing records, and re-add NOT NULL constraint';
    }

    public function up(Schema $schema): void
    {
        // Add the column without the NOT NULL constraint initially
        $this->addSql('ALTER TABLE ticket ADD cancelled BOOLEAN DEFAULT false');

        // Update existing records to have cancelled = false for all existing tickets
        $this->addSql('UPDATE ticket SET cancelled = false WHERE cancelled IS NULL');

        // Re-add the NOT NULL constraint
        $this->addSql('ALTER TABLE ticket ALTER COLUMN cancelled SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // Remove the cancelled column
        $this->addSql('ALTER TABLE ticket DROP cancelled');
    }
}
