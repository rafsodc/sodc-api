<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720151525 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('ALTER TABLE basket DROP CONSTRAINT basket_owner_id_fkey');
        $this->addSql('ALTER TABLE basket ALTER owner_id SET NOT NULL');
        $this->addSql('ALTER TABLE password_token DROP CONSTRAINT password_token_user_id_fkey');
        $this->addSql('ALTER TABLE password_token ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT ticket_owner_id_fkey');
        $this->addSql('ALTER TABLE ticket ALTER owner_id SET NOT NULL');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT ticket_user_temp_user_id_fkey');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT ticket_user_user_id_fkey');
        $this->addSql('ALTER TABLE ticket_user ALTER user_id SET NOT NULL');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_user ADD PRIMARY KEY (ticket_id, user_id)');
        //$this->addSql('DROP INDEX uniq_8d93d649d17f50a6');
        //$this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE "user" DROP int_id');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE user_notification DROP CONSTRAINT user_notification_user_id_fkey');
        $this->addSql('ALTER TABLE user_notification ALTER user_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE basket ALTER owner_id DROP NOT NULL');
        $this->addSql('DROP INDEX user_pkey');
        $this->addSql('ALTER TABLE "user" ADD int_id INT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d649d17f50a6 ON "user" (id)');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (int_id)');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT FK_BF48C371A76ED395');
        $this->addSql('DROP INDEX "primary"');
        $this->addSql('ALTER TABLE ticket_user ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT ticket_user_temp_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT ticket_user_user_id_fkey FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_notification ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE password_token ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE ticket ALTER owner_id DROP NOT NULL');
    }
}
