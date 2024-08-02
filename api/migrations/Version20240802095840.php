<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\DBAL\Types\Types;
use Doctrine\DBAL\ParameterType;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240802095840 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create subscription and associate with users who are subscribed';
    }

    public function up(Schema $schema): void
    {
        // Create the tables
        $this->addSql('CREATE TABLE subscription (uuid UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('COMMENT ON COLUMN subscription.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE user_subscription (uuid UUID NOT NULL, owner_id UUID NOT NULL, subscription_id UUID NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_EAF927517E3C61F9 ON user_subscription (owner_id)');
        $this->addSql('CREATE INDEX IDX_EAF927519A1887DC ON user_subscription (subscription_id)');
        $this->addSql('COMMENT ON COLUMN user_subscription.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_subscription.owner_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN user_subscription.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_EAF927517E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_EAF927519A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Create the subscription
        $subscriptionUuid = $this->generateUuid();
        $this->addSql('INSERT INTO subscription (uuid, name) VALUES (:uuid, :name)', [
            'uuid' => $subscriptionUuid,
            'name' => 'Default Subscription',
        ], [
            'uuid' => ParameterType::STRING,
            'name' => ParameterType::STRING,
        ]);

        // Associate the subscription with users who have 'isSubscribed' set to true
        $users = $this->connection->fetchAllAssociative('SELECT uuid FROM "user" WHERE is_subscribed = TRUE');

        foreach ($users as $user) {
            $userSubscriptionUuid = $this->generateUuid();
            $this->addSql('INSERT INTO user_subscription (uuid, owner_id, subscription_id) VALUES (:uuid, :ownerId, :subscriptionId)', [
                'uuid' => $userSubscriptionUuid,
                'ownerId' => $user['uuid'],
                'subscriptionId' => $subscriptionUuid,
            ], [
                'uuid' => ParameterType::STRING,
                'ownerId' => ParameterType::STRING,
                'subscriptionId' => ParameterType::STRING,
            ]);
        }
    }

    public function down(Schema $schema): void
    {
        // Drop the tables
        $this->addSql('ALTER TABLE user_subscription DROP CONSTRAINT FK_EAF927517E3C61F9');
        $this->addSql('ALTER TABLE user_subscription DROP CONSTRAINT FK_EAF927519A1887DC');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE user_subscription');
    }

    private function generateUuid(): string
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}
