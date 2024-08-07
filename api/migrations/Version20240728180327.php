<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240728180327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ipgreturn ADD client_return BOOLEAN');
        $this->addSql('UPDATE ipgreturn SET client_return = false WHERE client_return IS NULL');
        $this->addSql('ALTER TABLE ipgreturn ALTER client_return SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your need
        $this->addSql('ALTER TABLE ipgreturn DROP client_return');
    }
}
