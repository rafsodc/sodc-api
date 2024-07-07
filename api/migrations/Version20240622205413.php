<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240622205413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE notify_message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE notify_message (id UUID NOT NULL, roles TEXT NOT NULL, data JSON NOT NULL, template_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN notify_message.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN notify_message.roles IS \'(DC2Type:array)\'');
        $this->addSql('COMMENT ON COLUMN notify_message.template_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE notify_message_id_seq CASCADE');
        $this->addSql('DROP TABLE notify_message');
    }
}
