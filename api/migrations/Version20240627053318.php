<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240627053318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT fk_3f980ac87e3c61f9');
        $this->addSql('DROP INDEX idx_3f980ac87e3c61f9');
        $this->addSql('ALTER TABLE user_notification RENAME COLUMN owner_id TO user_id');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3F980AC8A76ED395 ON user_notification (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT FK_3F980AC8A76ED395');
        $this->addSql('DROP INDEX IDX_3F980AC8A76ED395');
        $this->addSql('ALTER TABLE user_notification RENAME COLUMN user_id TO owner_id');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT fk_3f980ac87e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3f980ac87e3c61f9 ON user_notification (owner_id)');
    }
}
