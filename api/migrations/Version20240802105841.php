<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240802105841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_8d93d64951fc1c95');
        $this->addSql('ALTER TABLE "user" DROP unsubscribe_uuid');
        $this->addSql('ALTER INDEX idx_eaf927517e3c61f9 RENAME TO IDX_230A18D17E3C61F9');
        $this->addSql('ALTER INDEX idx_eaf927519a1887dc RENAME TO IDX_230A18D19A1887DC');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ADD unsubscribe_uuid UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN "user".unsubscribe_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX uniq_8d93d64951fc1c95 ON "user" (unsubscribe_uuid)');
        $this->addSql('ALTER INDEX idx_230a18d17e3c61f9 RENAME TO idx_eaf927517e3c61f9');
        $this->addSql('ALTER INDEX idx_230a18d19a1887dc RENAME TO idx_eaf927519a1887dc');
    }
}
