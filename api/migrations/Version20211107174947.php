<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107174947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE agendum_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE media_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE speaker_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agendum (id INT NOT NULL, event_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, start DATE NOT NULL, finish DATE NOT NULL, hidden BOOLEAN NOT NULL, break BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_1E22C6A71F7E88B ON agendum (event_id)');
        $this->addSql('CREATE TABLE agendum_speaker (agendum_id INT NOT NULL, speaker_id INT NOT NULL, PRIMARY KEY(agendum_id, speaker_id))');
        $this->addSql('CREATE INDEX IDX_201746BE5434633D ON agendum_speaker (agendum_id)');
        $this->addSql('CREATE INDEX IDX_201746BED04A0F27 ON agendum_speaker (speaker_id)');
        $this->addSql('CREATE TABLE media (id INT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, size INT NOT NULL, data TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE speaker (id INT NOT NULL, photograph_id INT DEFAULT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, biography TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_7B85DB61D8BBBEC7 ON speaker (photograph_id)');
        $this->addSql('ALTER TABLE agendum ADD CONSTRAINT FK_1E22C6A71F7E88B FOREIGN KEY (event_id) REFERENCES "event" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agendum_speaker ADD CONSTRAINT FK_201746BE5434633D FOREIGN KEY (agendum_id) REFERENCES agendum (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agendum_speaker ADD CONSTRAINT FK_201746BED04A0F27 FOREIGN KEY (speaker_id) REFERENCES speaker (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE speaker ADD CONSTRAINT FK_7B85DB61D8BBBEC7 FOREIGN KEY (photograph_id) REFERENCES media (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE agendum_speaker DROP CONSTRAINT FK_201746BE5434633D');
        $this->addSql('ALTER TABLE speaker DROP CONSTRAINT FK_7B85DB61D8BBBEC7');
        $this->addSql('ALTER TABLE agendum_speaker DROP CONSTRAINT FK_201746BED04A0F27');
        $this->addSql('DROP SEQUENCE agendum_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE media_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE speaker_id_seq CASCADE');
        $this->addSql('DROP TABLE agendum');
        $this->addSql('DROP TABLE agendum_speaker');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE speaker');
    }
}
