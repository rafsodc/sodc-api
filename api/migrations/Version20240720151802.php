<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720151802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket DROP CONSTRAINT basket_temp_owner_id_fkey');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT password_token_temp_user_id_fkey');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT ticket_temp_owner_id_fkey');
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT user_notification_temp_user_id_fkey');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT fk_bf48c371a76ed395');
        $this->addSql('DROP INDEX uniq_8d93d649d17f50a6');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649d17f50a6 ON "user" (id)');
    }
}
