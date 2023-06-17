<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230617220636 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket_type ADD uuid UUID');
        $this->addSql('UPDATE ticket_type SET uuid = uuid_in(overlay(overlay(md5(random()::text || \':\' || random()::text) placing \'4\' from 13) placing to_hex(floor(random()*(11-8+1) + 8)::int)::text from 17)::cstring);');
        $this->addSql('ALTER TABLE ticket_type ALTER COLUMN uuid SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN ticket_type.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BE054211D17F50A6 ON ticket_type (uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_BE054211D17F50A6');
        $this->addSql('ALTER TABLE ticket_type DROP uuid');
    }
}
