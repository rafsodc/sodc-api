<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221023174808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE speaker DROP CONSTRAINT fk_7b85db61d8bbbec7');
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media_object_id_seq CASCADE');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE media_object');
        $this->addSql('DROP INDEX uniq_7b85db61d8bbbec7');
        $this->addSql('ALTER TABLE speaker DROP photograph_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_object_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, data TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE media_object (id INT NOT NULL, file_path VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE speaker ADD photograph_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE speaker ADD CONSTRAINT fk_7b85db61d8bbbec7 FOREIGN KEY (photograph_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_7b85db61d8bbbec7 ON speaker (photograph_id)');
    }
}
