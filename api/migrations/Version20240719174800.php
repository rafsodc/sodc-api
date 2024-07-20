<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719174800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX uniq_8d93d64951fc1c95');
        $this->addSql('ALTER TABLE "user" DROP unsubscribe_uuid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE "user" ADD unsubscribe_uuid UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".unsubscribe_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d64951fc1c95 ON "user" (unsubscribe_uuid)');
    }
}
