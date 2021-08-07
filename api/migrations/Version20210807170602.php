<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210807170602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE rank_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE rank (id INT NOT NULL, rank VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE "user" ADD mobile_number VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD first_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD post_nominals VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD service_number VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE "user" ADD modnet_email VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE rank_id_seq CASCADE');
        $this->addSql('DROP TABLE rank');
        $this->addSql('ALTER TABLE "user" DROP mobile_number');
        $this->addSql('ALTER TABLE "user" DROP first_name');
        $this->addSql('ALTER TABLE "user" DROP last_name');
        $this->addSql('ALTER TABLE "user" DROP post_nominals');
        $this->addSql('ALTER TABLE "user" DROP service_number');
        $this->addSql('ALTER TABLE "user" DROP modnet_email');
    }
}
