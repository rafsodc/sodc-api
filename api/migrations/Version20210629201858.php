<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629201858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE basket ADD transaction_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2246507B2FC0CB0F ON basket (transaction_id)');
        $this->addSql('ALTER TABLE transaction DROP CONSTRAINT fk_723705d11be1fb52');
        $this->addSql('DROP INDEX uniq_723705d11be1fb52');
        $this->addSql('ALTER TABLE transaction DROP basket_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE basket DROP CONSTRAINT FK_2246507B2FC0CB0F');
        $this->addSql('DROP INDEX UNIQ_2246507B2FC0CB0F');
        $this->addSql('ALTER TABLE basket DROP transaction_id');
        $this->addSql('ALTER TABLE transaction ADD basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT fk_723705d11be1fb52 FOREIGN KEY (basket_id) REFERENCES basket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_723705d11be1fb52 ON transaction (basket_id)');
    }
}
