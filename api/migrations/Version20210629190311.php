<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629190311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE basket_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE basket (id INT NOT NULL, owner_id INT NOT NULL, event_id INT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2246507B7E3C61F9 ON basket (owner_id)');
        $this->addSql('CREATE INDEX IDX_2246507B71F7E88B ON basket (event_id)');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B71F7E88B FOREIGN KEY (event_id) REFERENCES "event" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d17e3c61f9');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d171f7e88b');
        $this->addSql('DROP INDEX idx_723705d171f7e88b');
        $this->addSql('DROP INDEX idx_723705d17e3c61f9');
        $this->addSql('ALTER TABLE transaction ADD status VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE transaction DROP owner_id');
        $this->addSql('ALTER TABLE transaction DROP event_id');
        $this->addSql('ALTER TABLE transaction DROP amount');
        $this->addSql('ALTER TABLE transaction DROP is_paid');
        $this->addSql('ALTER TABLE transaction DROP is_valid');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE basket_id_seq CASCADE');
        $this->addSql('DROP TABLE basket');
        $this->addSql('ALTER TABLE transaction ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD event_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD amount DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD is_paid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD is_valid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction DROP status');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d17e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d171f7e88b FOREIGN KEY (event_id) REFERENCES event (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_723705d171f7e88b ON transaction (event_id)');
        $this->addSql('CREATE INDEX idx_723705d17e3c61f9 ON transaction (owner_id)');
    }
}
