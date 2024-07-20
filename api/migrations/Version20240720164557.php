<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720164557 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id TO uuid');
        // $this->addSql('ALTER TABLE basket DROP CONSTRAINT FK_2246507B7E3C61F9');
        // $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE password_token DROP CONSTRAINT FK_BEAB6C24A76ED395');
        // $this->addSql('ALTER TABLE password_token ADD CONSTRAINT FK_BEAB6C24A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA37E3C61F9');
        // $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT fk_bf48c371a76ed395');
        // $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT fk_bf48c371a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        // $this->addSql('DROP INDEX "primary"');
        // $this->addSql('ALTER TABLE "user" ADD uuid UUID NOT NULL');
        // $this->addSql('ALTER TABLE "user" ALTER id TYPE INT');
        // $this->addSql('ALTER TABLE "user" ALTER id DROP DEFAULT');
        // $this->addSql('COMMENT ON COLUMN "user".uuid IS \'(DC2Type:uuid)\'');
        // $this->addSql('COMMENT ON COLUMN "user".id IS NULL');
        // $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (uuid)');
        // $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT FK_3F980AC8A76ED395');
        // $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT FK_3F980AC8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
