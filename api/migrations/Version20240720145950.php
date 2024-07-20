<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720145950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD temp_owner_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN basket.temp_owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE password_token ADD temp_user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN password_token.temp_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE ticket ADD temp_owner_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN ticket.temp_owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE ticket_user ADD temp_user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN ticket_user.temp_user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_notification ADD temp_user_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN user_notification.temp_user_id IS \'(DC2Type:uuid)\'');

        $this->addSql('UPDATE basket SET temp_owner_id = (SELECT uuid FROM "user" WHERE "user".id = basket.owner_id)');
        $this->addSql('UPDATE password_token SET temp_user_id = (SELECT uuid FROM "user" WHERE "user".id = password_token.user_id)');
        $this->addSql('UPDATE ticket SET temp_owner_id = (SELECT uuid FROM "user" WHERE "user".id = ticket.owner_id)');
        $this->addSql('UPDATE ticket_user SET temp_user_id = (SELECT uuid FROM "user" WHERE "user".id = ticket_user.user_id)');
        $this->addSql('UPDATE user_notification SET temp_user_id = (SELECT uuid FROM "user" WHERE "user".id = user_notification.user_id)');

        $this->addSql('ALTER TABLE basket DROP CONSTRAINT IF EXISTS basket_owner_id_fkey');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT IF EXISTS password_token_user_id_fkey');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT IF EXISTS ticket_owner_id_fkey');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT IF EXISTS ticket_user_user_id_fkey');
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT IF EXISTS user_notification_user_id_fkey');
    
        // Add foreign key constraints to temporary UUID columns
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT basket_temp_owner_id_fkey FOREIGN KEY (temp_owner_id) REFERENCES "user" (uuid)');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT password_token_temp_user_id_fkey FOREIGN KEY (temp_user_id) REFERENCES "user" (uuid)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT ticket_temp_owner_id_fkey FOREIGN KEY (temp_owner_id) REFERENCES "user" (uuid)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT ticket_user_temp_user_id_fkey FOREIGN KEY (temp_user_id) REFERENCES "user" (uuid)');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT user_notification_temp_user_id_fkey FOREIGN KEY (temp_user_id) REFERENCES "user" (uuid)');

        // Drop old integer columns and rename UUID columns
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id TO int_id');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN uuid TO id');

        $this->addSql('ALTER TABLE basket DROP COLUMN owner_id');
        $this->addSql('ALTER TABLE basket RENAME COLUMN temp_owner_id TO owner_id');

        $this->addSql('ALTER TABLE password_token DROP COLUMN user_id');
        $this->addSql('ALTER TABLE password_token RENAME COLUMN temp_user_id TO user_id');

        $this->addSql('ALTER TABLE ticket DROP COLUMN owner_id');
        $this->addSql('ALTER TABLE ticket RENAME COLUMN temp_owner_id TO owner_id');

        $this->addSql('ALTER TABLE ticket_user DROP COLUMN user_id');
        $this->addSql('ALTER TABLE ticket_user RENAME COLUMN temp_user_id TO user_id');

        $this->addSql('ALTER TABLE user_notification DROP COLUMN user_id');
        $this->addSql('ALTER TABLE user_notification RENAME COLUMN temp_user_id TO user_id');

        // Add foreign key constraints to final UUID columns
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT basket_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES "user" (id)');
        $this->addSql('ALTER TABLE password_token ADD CONSTRAINT password_token_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT ticket_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES "user" (id)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT ticket_user_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id)');
        $this->addSql('ALTER TABLE user_notification ADD CONSTRAINT user_notification_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id)');

    
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE password_token DROP temp_user_id');
        $this->addSql('ALTER TABLE ticket DROP temp_owner_id');
        $this->addSql('ALTER TABLE basket DROP temp_owner_id');
        $this->addSql('ALTER TABLE user_notification DROP temp_user_id');
    }
}
