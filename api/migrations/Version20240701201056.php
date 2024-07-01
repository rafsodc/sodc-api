<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240701201056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Step 1: Add the column allowing null values
        $this->addSql('ALTER TABLE "user" ADD unsubscribe_uuid UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".unsubscribe_uuid IS \'(DC2Type:uuid)\'');

        // Step 2: Set UUIDs for all existing users
        $users = $this->connection->fetchAllAssociative('SELECT id FROM "user"');
        foreach ($users as $user) {
            $uuid = Uuid::uuid4()->toString();
            $this->addSql('UPDATE "user" SET unsubscribe_uuid = ? WHERE id = ?', [$uuid, $user['id']]);
        }

        // Step 3: Alter the column to disallow null values
        $this->addSql('ALTER TABLE "user" ALTER COLUMN unsubscribe_uuid SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64951FC1C95 ON "user" (unsubscribe_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D64951FC1C95');
        $this->addSql('ALTER TABLE "user" DROP unsubscribe_uuid');
    }
}
