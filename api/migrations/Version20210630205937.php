<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210630205937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE basket_ticket (basket_id INT NOT NULL, ticket_id INT NOT NULL, PRIMARY KEY(basket_id, ticket_id))');
        $this->addSql('CREATE INDEX IDX_51FA0C5A1BE1FB52 ON basket_ticket (basket_id)');
        $this->addSql('CREATE INDEX IDX_51FA0C5A700047D2 ON basket_ticket (ticket_id)');
        $this->addSql('ALTER TABLE basket_ticket ADD CONSTRAINT FK_51FA0C5A1BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE basket_ticket ADD CONSTRAINT FK_51FA0C5A700047D2 FOREIGN KEY (ticket_id) REFERENCES ticket (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ticket DROP CONSTRAINT fk_97a0ada31be1fb52');
        $this->addSql('DROP INDEX idx_97a0ada31be1fb52');
        $this->addSql('ALTER TABLE ticket DROP basket_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE basket_ticket');
        $this->addSql('ALTER TABLE ticket ADD basket_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT fk_97a0ada31be1fb52 FOREIGN KEY (basket_id) REFERENCES basket (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_97a0ada31be1fb52 ON ticket (basket_id)');
    }
}
