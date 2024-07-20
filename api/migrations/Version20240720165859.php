<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240720165859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT FK_BF48C371700047D2');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT FK_BF48C371A76ED395');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT FK_BF48C371A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT fk_bf48c371700047d2');
        $this->addSql('ALTER TABLE ticket_user DROP CONSTRAINT fk_bf48c371a76ed395');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT fk_bf48c371700047d2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket_user ADD CONSTRAINT fk_bf48c371a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (uuid) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
