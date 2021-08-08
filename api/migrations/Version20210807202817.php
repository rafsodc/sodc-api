<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807202817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ADD rank_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD work_details TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD is_shared BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D6497616678F FOREIGN KEY (rank_id) REFERENCES rank (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8D93D6497616678F ON "user" (rank_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D6497616678F');
        $this->addSql('DROP INDEX IDX_8D93D6497616678F');
        $this->addSql('ALTER TABLE "user" DROP rank_id');
        $this->addSql('ALTER TABLE "user" DROP work_details');
        $this->addSql('ALTER TABLE "user" DROP is_shared');
    }
}
