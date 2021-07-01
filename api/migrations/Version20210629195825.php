<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210629195825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT fk_97a0ada32fc0cb0f');
        $this->addSql('DROP INDEX idx_97a0ada32fc0cb0f');
        $this->addSql('ALTER TABLE ticket RENAME COLUMN transaction_id TO basket_id');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA31BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_97A0ADA31BE1FB52 ON ticket (basket_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT FK_97A0ADA31BE1FB52');
        $this->addSql('DROP INDEX IDX_97A0ADA31BE1FB52');
        $this->addSql('ALTER TABLE ticket RENAME COLUMN basket_id TO transaction_id');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT fk_97a0ada32fc0cb0f FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_97a0ada32fc0cb0f ON ticket (transaction_id)');
    }
}
