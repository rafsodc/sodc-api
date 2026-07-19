<?php

namespace App\Tests\Unit\Service;

use App\Entity\Rank;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Service\LegacyUserPreflightBuilder;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LegacyUserPreflightBuilderTest extends TestCase
{
    public function testBuildsDeterministicNonPiiAggregateReport(): void
    {
        $news = $this->subscription('00000000-0000-0000-0000-000000000002', 'News');
        $events = $this->subscription('00000000-0000-0000-0000-000000000001', 'Events');

        $member = $this->user('00000000-0000-0000-0000-000000000010', 'Member@Example.org', true, true);
        $member->setOldUid(7);
        $member->setRoles(['ROLE_USER', 'ROLE_MEMBER']);
        $member->setFirstName('Example');
        $member->setLastName('Member');
        $member->setServiceNumber('123');
        $member->setMobileNumber('07123456789');
        $member->setPassword(password_hash('secret', PASSWORD_BCRYPT));
        $member->setRank($this->rank(1, 'Sqn Ldr'));
        $member->addUserSubscription($this->relationship($events));
        $member->addUserSubscription($this->relationship($news));
        $member->addUserSubscription($this->relationship($news));

        $nonMember = $this->user('00000000-0000-0000-0000-000000000011', ' member@example.org ', false, false);
        $nonMember->setOldUid(7);
        $nonMember->setRoles([]);
        $nonMember->setFirstName('');
        $nonMember->setLastName(null);
        $nonMember->setPassword('not-a-supported-hash');

        $report = (new LegacyUserPreflightBuilder())->build(
            [$member, $nonMember],
            [$news, $events],
            new \DateTimeImmutable('2026-07-19T14:00:00+00:00')
        );

        self::assertSame(LegacyUserPreflightBuilder::SCHEMA_VERSION, $report['schemaVersion']);
        self::assertSame('2026-07-19T14:00:00Z', $report['generatedAt']);
        self::assertSame(2, $report['totalUsers']);
        self::assertSame(1, $report['emailQuality']['duplicateNormalized']);
        self::assertSame(1, $report['emailQuality']['leadingOrTrailingWhitespace']);
        self::assertSame(1, $report['identityQuality']['oldUid']['duplicate']);
        self::assertSame(1, $report['passwords']['supported']);
        self::assertSame(1, $report['passwords']['unsupportedOrMalformed']);
        self::assertSame(1, $report['membershipConsistency']['memberFlagAndRole']);
        self::assertSame(1, $report['membershipConsistency']['neither']);
        self::assertSame(1, $report['relationshipQuality']['duplicateUserSubscriptionPairs']);
        self::assertSame(2, $report['subscriptionSummary']['relationshipCount']);
        self::assertSame([
            ['count' => 0, 'users' => 1],
            ['count' => 2, 'users' => 1],
        ], $report['subscriptionSummary']['subscriptionsPerUser']['distribution']);
        self::assertSame('00000000-0000-0000-0000-000000000001', $report['subscriptions'][0]['id']);
        self::assertSame(1, $report['subscriptions'][0]['members']);
        self::assertSame(1, $report['subscriptionCorrelation'][0]['users']);
        self::assertSame(1, $report['subscriptionCorrelation'][3]['users']);

        $encoded = json_encode($report, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('Member@Example.org', $encoded);
        self::assertStringNotContainsString('07123456789', $encoded);
        self::assertStringNotContainsString('$2y$', $encoded);
    }

    private function user(string $uuid, string $email, bool $isMember, bool $isSubscribed): User
    {
        $user = new User();
        $user->setUuid(Uuid::fromString($uuid));
        $this->setProperty($user, 'email', $email);
        $user->setIsMember($isMember);
        $user->setIsSubscribed($isSubscribed);
        $user->setIsShared(true);

        return $user;
    }

    private function subscription(string $uuid, string $name): Subscription
    {
        $subscription = new Subscription();
        $subscription->setUuid(Uuid::fromString($uuid));
        $subscription->setName($name);

        return $subscription;
    }

    private function relationship(Subscription $subscription): UserSubscription
    {
        return (new UserSubscription())->setSubscription($subscription);
    }

    private function rank(int $id, string $value): Rank
    {
        $rank = (new Rank())->setRank($value);
        $this->setProperty($rank, 'id', $id);

        return $rank;
    }

    private function setProperty(object $object, string $property, $value): void
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}
