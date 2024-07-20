<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720152320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2246507B7E3C61F9 ON basket (owner_id)');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BEAB6C24A76ED395 ON password_token (user_id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_97A0ADA37E3C61F9 ON ticket (owner_id)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BF48C371A76ED395 ON ticket_user (user_id)');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_3F980AC8A76ED395 ON user_notification (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE basket DROP CONSTRAINT FK_2246507B7E3C61F9');
        $this->addSql('DROP INDEX IDX_2246507B7E3C61F9');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT FK_BF48C371A76ED395');
        $this->addSql('DROP INDEX IDX_BF48C371A76ED395');
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT FK_3F980AC8A76ED395');
        $this->addSql('DROP INDEX IDX_3F980AC8A76ED395');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT FK_BEAB6C24A76ED395');
        $this->addSql('DROP INDEX IDX_BEAB6C24A76ED395');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA37E3C61F9');
        $this->addSql('DROP INDEX IDX_97A0ADA37E3C61F9');
    }
}
