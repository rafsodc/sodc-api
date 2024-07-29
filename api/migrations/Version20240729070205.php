<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729070205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add the column allowing nulls
        $this->addSql('ALTER TABLE "user" ADD unsubscribe_uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".unsubscribe_uuid IS \'(DC2Type:uuid)\'');            
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP unsubscribe_uuid');
    }
}
