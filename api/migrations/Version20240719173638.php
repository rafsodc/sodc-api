<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719173638 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Step 1: Add the column allowing null values
        $this->addSql('ALTER TABLE "user" ADD uuid UUID');
        $this->addSql('COMMENT ON COLUMN "user".uuid IS \'(DC2Type:uuid)\'');

        // Step 2: Set UUIDs for all existing users
        $users = $this->connection->fetchAllAssociative('SELECT id FROM "user"');
        foreach ($users as $user) {
            $uuid = Uuid::uuid4()->toString();
            $this->addSql('UPDATE "user" SET uuid = ? WHERE id = ?', [$uuid, $user['id']]);
        }
        
        // Step 3: Alter the column to disallow null values
        $this->addSql('ALTER TABLE "user" ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8D93D649D17F50A6');
        $this->addSql('ALTER TABLE "user" DROP uuid');
    }
}
