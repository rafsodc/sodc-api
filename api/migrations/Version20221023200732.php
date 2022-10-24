<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023200732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE speaker ADD photograph_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE speaker ADD CONSTRAINT FK_7B85DB61D8BBBEC7 FOREIGN KEY (photograph_id) REFERENCES media_object (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B85DB61D8BBBEC7 ON speaker (photograph_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE speaker DROP CONSTRAINT FK_7B85DB61D8BBBEC7');
        $this->addSql('DROP INDEX UNIQ_7B85DB61D8BBBEC7');
        $this->addSql('ALTER TABLE speaker DROP photograph_id');
    }
}
