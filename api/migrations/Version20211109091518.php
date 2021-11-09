<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109091518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agendum_speaker DROP CONSTRAINT fk_201746be5434633d');
        $this->addSql('ALTER TABLE speaker DROP CONSTRAINT fk_7b85db61d8bbbec7');
        $this->addSql('ALTER TABLE agendum_speaker DROP CONSTRAINT fk_201746bed04a0f27');
        $this->addSql('DROP SEQUENCE speaker_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE agendum_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('CREATE TABLE ticket_user (ticket_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(ticket_id, user_id))');
        $this->addSql('CREATE INDEX IDX_BF48C371700047D2 ON ticket_user (ticket_id)');
        $this->addSql('CREATE INDEX IDX_BF48C371A76ED395 ON ticket_user (user_id)');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE agendum');
        $this->addSql('DROP TABLE agendum_speaker');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE speaker');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE speaker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE agendum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agendum (id INT NOT NULL, event_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, start DATE NOT NULL, finish DATE NOT NULL, hidden BOOLEAN NOT NULL, break BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_1e22c6a71f7e88b ON agendum (event_id)');
        $this->addSql('CREATE TABLE agendum_speaker (agendum_id INT NOT NULL, speaker_id INT NOT NULL, PRIMARY KEY(agendum_id, speaker_id))');
        $this->addSql('CREATE INDEX idx_201746be5434633d ON agendum_speaker (agendum_id)');
        $this->addSql('CREATE INDEX idx_201746bed04a0f27 ON agendum_speaker (speaker_id)');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, data TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE speaker (id INT NOT NULL, photograph_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, biography TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_7b85db61d8bbbec7 ON speaker (photograph_id)');
        $this->addSql('ALTER TABLE agendum ADD CONSTRAINT fk_1e22c6a71f7e88b FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agendum_speaker ADD CONSTRAINT fk_201746be5434633d FOREIGN KEY (agendum_id) REFERENCES agendum (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agendum_speaker ADD CONSTRAINT fk_201746bed04a0f27 FOREIGN KEY (speaker_id) REFERENCES speaker (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE speaker ADD CONSTRAINT fk_7b85db61d8bbbec7 FOREIGN KEY (photograph_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE ticket_user');
    }
}
