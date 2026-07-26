<?php

namespace App\Tests\Unit\Service;

use App\Entity\Rank;
use App\Entity\Subscription;
use App\Entity\User;
use App\Entity\UserSubscription;
use App\Service\LegacyUserPreflightBuilder;
use App\Service\LegacyUserRecordTransformer;
use App\Service\LegacyUserSchemaCounter;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LegacyUserPreflightBuilderTest extends TestCase
{
    public function testCountsOnlyCanonicalExportSchemaFields(): void
    {
        $subscription = $this->subscription('00000000-0000-0000-0000-000000000001', 'News');

        $member = $this->user('00000000-0000-0000-0000-000000000010', 'member@example.org', true);
        $member->setOldUid(7);
        $member->setRoles(['ROLE_USER', 'ROLE_SERVING']);
        $member->setFirstName('Example');
        $member->setLastName('Member');
        $member->setServiceNumber('123456');
        $member->setMobileNumber('07123456789');
        $member->setPhoneNumber('must-not-be-used');
        $member->setRank($this->rank(1, 'Sqn Ldr'));
        $member->setPassword(password_hash('secret', PASSWORD_BCRYPT));
        $member->addUserSubscription($this->relationship($subscription));

        $pending = $this->user('00000000-0000-0000-0000-000000000011', 'pending@example.org', false);
        $pending->setFirstName('  ');
        $pending->setPhoneNumber('01234567890');
        $pending->setPassword('legacy-hash');

        $deleted = $this->user('00000000-0000-0000-0000-000000000012', 'deleted@example.org', true);
        $deleted->setRoles(['ROLE_DELETED']);

        $report = $this->builder()->build(
            [$member, $pending, $deleted],
            new \DateTimeImmutable('2026-07-19T14:00:00+00:00')
        );

        self::assertSame([
            'schemaVersion',
            'recordSchemaVersion',
            'generatedAt',
            'overall',
            'byOldUid',
        ], array_keys($report));
        self::assertSame([
            'legacyUserId',
            'oldUid',
            'email',
            'firstName',
            'lastName',
            'mobileNumber',
            'postNominals',
            'serviceNumber',
            'rank',
            'membershipStatus',
            'isShared',
            'hasSubscriptions',
            'passwordHash',
        ], array_keys($report['overall']['fields']));
        self::assertSame(LegacyUserPreflightBuilder::SCHEMA_VERSION, $report['schemaVersion']);
        self::assertSame(LegacyUserPreflightBuilder::RECORD_SCHEMA_VERSION, $report['recordSchemaVersion']);
        self::assertSame('2026-07-19T14:00:00Z', $report['generatedAt']);
        self::assertSame(2, $report['overall']['recordCount']);
        self::assertSame([
            'usersComplete' => 1,
            'usersMissingAny' => 1,
            'missingByField' => [
                'email' => 0,
                'firstName' => 1,
                'lastName' => 1,
                'serviceNumber' => 1,
            ],
        ], $report['overall']['requiredFields']);
        self::assertSame(1, $report['byOldUid']['set']['recordCount']);
        self::assertSame(1, $report['byOldUid']['missing']['recordCount']);
        self::assertSame(1, $report['byOldUid']['set']['requiredFields']['usersComplete']);
        self::assertSame(1, $report['byOldUid']['missing']['requiredFields']['usersMissingAny']);
        self::assertSame(['null' => 0, 'blank' => 0, 'present' => 2], $report['overall']['fields']['mobileNumber']);
        self::assertSame(1, $report['overall']['fields']['rank']['values']['Squadron Leader']);
        self::assertSame(1, $report['overall']['fields']['rank']['null']);
        self::assertSame(1, $report['overall']['fields']['membershipStatus']['REGULAR']);
        self::assertSame(1, $report['overall']['fields']['membershipStatus']['PENDING']);
        self::assertSame(['true' => 1, 'false' => 1], $report['overall']['fields']['hasSubscriptions']);

        $encoded = json_encode($report, JSON_THROW_ON_ERROR);
        self::assertStringNotContainsString('member@example.org', $encoded);
        self::assertStringNotContainsString('07123456789', $encoded);
        self::assertStringNotContainsString('01234567890', $encoded);
        self::assertStringNotContainsString('ROLE_', $encoded);
        self::assertStringNotContainsString('isSubscribed', $encoded);
    }

    public function testAppliesMembershipStatusPriority(): void
    {
        $users = [
            $this->userWithRoles(20, ['ROLE_DELETED', 'ROLE_RESIGNED'], true),
            $this->userWithRoles(21, ['ROLE_RETIRED', 'ROLE_SERVING', 'ROLE_LOST', 'ROLE_DECEASED', 'ROLE_RESIGNED'], true),
            $this->userWithRoles(22, ['ROLE_LOST', 'ROLE_DECEASED'], true),
            $this->userWithRoles(23, ['ROLE_LOST'], true),
            $this->userWithRoles(24, ['ROLE_RETIRED', 'ROLE_SERVING'], true),
            $this->userWithRoles(25, ['ROLE_RETIRED'], true),
            $this->userWithRoles(26, [], false),
            $this->userWithRoles(27, [], true),
        ];

        $report = $this->builder()->build($users, new \DateTimeImmutable('2026-07-19T14:00:00Z'));

        self::assertSame(7, $report['overall']['recordCount']);
        self::assertSame([
            'PENDING' => 1,
            'REGULAR' => 2,
            'RETIRED' => 1,
            'RESIGNED' => 1,
            'LOST' => 1,
            'DECEASED' => 1,
        ], $report['overall']['fields']['membershipStatus']);
    }

    public function testBlankMobileNumberDoesNotUsePhoneFallback(): void
    {
        $user = $this->userWithRoles(28, [], true);
        $user->setMobileNumber('');
        $user->setPhoneNumber('01234567890');

        $report = $this->builder()->build([$user], new \DateTimeImmutable('2026-07-19T14:00:00Z'));

        self::assertSame(['null' => 0, 'blank' => 1, 'present' => 0], $report['overall']['fields']['mobileNumber']);
    }

    public function testMapsEveryApprovedRank(): void
    {
        $mappings = [
            1 => ['Sqn Ldr', 'Squadron Leader'],
            2 => ['ACM', 'Air Chief Marshal'],
            3 => ['AM', 'Air Marshal'],
            7 => ['AVM', 'Air Vice-Marshal'],
            8 => ['Air Cdre', 'Air Commodore'],
            9 => ['Gp Capt', 'Group Captain'],
            10 => ['Wg Cdr', 'Wing Commander'],
            11 => ['Flt Lt', 'Flight Lieutenant'],
            12 => ['Mr', 'Mr'],
            13 => ['Dr', 'Dr'],
            14 => ['Fg Off', 'Flying Officer'],
            15 => ['Mrs', 'Mrs'],
            16 => ['Ms', 'Ms'],
        ];
        $users = [];
        $index = 100;
        foreach ($mappings as $id => [$source]) {
            $user = $this->userWithRoles($index++, [], true);
            $user->setRank($this->rank($id, $source));
            $users[] = $user;
        }
        $report = $this->builder()->build($users, new \DateTimeImmutable('2026-07-19T14:00:00Z'));

        foreach ($mappings as [, $target]) {
            self::assertSame(1, $report['overall']['fields']['rank']['values'][$target]);
        }
        self::assertSame(0, $report['overall']['fields']['rank']['null']);
    }

    public function testRejectsInvalidRankMapping(): void
    {
        $user = $this->userWithRoles(200, [], true);
        $user->setRank($this->rank(1, 'Unexpected'));

        $this->expectException(\UnexpectedValueException::class);
        $this->builder()->build([$user], new \DateTimeImmutable('2026-07-19T14:00:00Z'));
    }

    private function builder(): LegacyUserPreflightBuilder
    {
        return new LegacyUserPreflightBuilder(new LegacyUserRecordTransformer(), new LegacyUserSchemaCounter());
    }

    private function user(string $uuid, string $email, bool $isMember): User
    {
        $user = new User();
        $user->setUuid(Uuid::fromString($uuid));
        $this->setProperty($user, 'email', $email);
        $user->setIsMember($isMember);
        $user->setIsSubscribed(true);
        $user->setIsShared(true);

        return $user;
    }

    private function userWithRoles(int $suffix, array $roles, bool $isMember): User
    {
        $user = $this->user(
            sprintf('00000000-0000-0000-0000-%012d', $suffix),
            sprintf('user%d@example.org', $suffix),
            $isMember
        );
        $user->setRoles($roles);
        $user->setIsMember($isMember);

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
