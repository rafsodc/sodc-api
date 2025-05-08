<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507214250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE bulk_notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE notification_return_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket DROP CONSTRAINT fk_2246507b2fc0cb0f
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_2246507b2fc0cb0f
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket DROP transaction_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket ALTER is_paid SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket RENAME COLUMN created_date TO created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ADD name VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ADD message TEXT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ADD sent BOOLEAN NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification DROP data
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification DROP template_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ALTER id TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN bulk_notification.id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contact RENAME COLUMN created_date TO created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD start_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD end_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD is_published BOOLEAN NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_alt VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_title VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_description VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_caption VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_credit VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_copyright VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_source VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_thumbnail VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_medium VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_large VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xlarge VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xxlarge VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xxxlarge VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_original VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_mime_type VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_size INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_width VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_height VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_aspect_ratio VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_orientation VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_color VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_format VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_extension VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_filename VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_url_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_thumbnail_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_medium_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_large_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xlarge_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xxlarge_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xxxlarge_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_original_path VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_thumbnail_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_medium_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_large_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xlarge_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xxlarge_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_xxxlarge_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD image_original_url VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP booking_open
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP booking_close
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP principal_speaker
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP sponsor
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ALTER description SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event RENAME COLUMN venue TO location
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD transaction_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD endpoint_transaction_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD ipg_transaction_id BIGINT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD currency INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD total DOUBLE PRECISION DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD fail_reason VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD client_return BOOLEAN NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD error VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD data JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ALTER id TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN notification_return.id IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ADD CONSTRAINT FK_6339C1522FC0CB0F FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_6339C1522FC0CB0F ON notification_return (transaction_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_notification ALTER bulk_notification_id TYPE INT
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_notification.bulk_notification_id IS NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE bulk_notification_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE notification_return_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contact RENAME COLUMN created_at TO created_date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" ADD date DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" ADD booking_open DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" ADD booking_close DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" ADD principal_speaker VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" ADD sponsor VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP start_date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP end_date
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP is_published
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_alt
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_title
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_description
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_caption
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_credit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_copyright
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_source
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_thumbnail
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_medium
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_large
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xlarge
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xxlarge
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xxxlarge
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_original
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_mime_type
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_size
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_width
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_height
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_aspect_ratio
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_orientation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_color
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_format
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_extension
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_filename
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_url_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_thumbnail_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_medium_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_large_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xlarge_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xxlarge_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xxxlarge_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_original_path
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_thumbnail_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_medium_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_large_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xlarge_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xxlarge_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_xxxlarge_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" DROP image_original_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" ALTER description DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "event" RENAME COLUMN location TO venue
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ADD data JSON NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ADD template_id UUID NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification DROP name
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification DROP message
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification DROP sent
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE bulk_notification ALTER id TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN bulk_notification.template_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN bulk_notification.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_notification ALTER bulk_notification_id TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN user_notification.bulk_notification_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket ADD transaction_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket ADD created_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket DROP created_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket DROP updated_at
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket ALTER is_paid DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE basket ADD CONSTRAINT fk_2246507b2fc0cb0f FOREIGN KEY (transaction_id) REFERENCES transaction (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uniq_2246507b2fc0cb0f ON basket (transaction_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP CONSTRAINT FK_6339C1522FC0CB0F
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_6339C1522FC0CB0F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP transaction_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP endpoint_transaction_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP ipg_transaction_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP currency
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP total
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP fail_reason
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP client_return
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP error
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return DROP data
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification_return ALTER id TYPE UUID
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN notification_return.id IS '(DC2Type:uuid)'
        SQL);
    }
}
