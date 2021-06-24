<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210623180812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD is_paid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD is_valid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction DROP paid');
        $this->addSql('ALTER TABLE transaction DROP valid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE transaction ADD paid BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD valid BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE transaction DROP is_paid');
        $this->addSql('ALTER TABLE transaction DROP is_valid');
    }
}
