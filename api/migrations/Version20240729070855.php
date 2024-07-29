<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Ramsey\Uuid\Uuid;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240729070855 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->populateUnsubscribeUuid();
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ALTER unsubscribe_uuid SET NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64951FC1C95 ON "user" (unsubscribe_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_8D93D64951FC1C95');
        $this->addSql('ALTER TABLE "user" ALTER unsubscribe_uuid DROP NOT NULL');
    }

    private function populateUnsubscribeUuid(): void
    {
        $connection = $this->connection;
        $result = $connection->executeQuery('SELECT uuid FROM "user" WHERE unsubscribe_uuid IS NULL');
        
        while ($row = $result->fetchAssociative()) {
            $uuid = Uuid::uuid4()->toString();
            $connection->executeStatement('UPDATE "user" SET unsubscribe_uuid = ? WHERE uuid = ?', [$uuid, $row['uuid']]);
        }
    }
}
