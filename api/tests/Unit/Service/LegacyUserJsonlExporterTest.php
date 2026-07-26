<?php

namespace App\Tests\Unit\Service;

use App\Entity\User;
use App\Service\LegacyUserJsonlExporter;
use App\Service\LegacyUserRecordTransformer;
use App\Service\LegacyUserSchemaCounter;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

final class LegacyUserJsonlExporterTest extends TestCase
{
    public function testExportsOnlyCanonicalRecordsInDeterministicFieldOrder(): void
    {
        $included = $this->user('00000000-0000-0000-0000-000000000001', ['ROLE_SERVING'], true);
        $included->setOldUid(42);
        $included->setFirstName('Example');
        $included->setLastName('Member');
        $included->setMobileNumber(null);
        $included->setPhoneNumber('01234567890');
        $included->setPassword('$2y$example');

        $deleted = $this->user('00000000-0000-0000-0000-000000000002', ['ROLE_DELETED'], true);
        $chunks = [];
        $exporter = new LegacyUserJsonlExporter(new LegacyUserRecordTransformer(), new LegacyUserSchemaCounter());

        $counts = $exporter->export([$included, $deleted], static function (string $chunk) use (&$chunks): void {
            $chunks[] = $chunk;
        });

        self::assertSame(1, $counts['recordCount']);
        self::assertCount(1, $chunks);
        self::assertStringEndsWith("\n", $chunks[0]);
        $record = json_decode(trim($chunks[0]), true, 512, JSON_THROW_ON_ERROR);
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
        ], array_keys($record));
        self::assertSame('01234567890', $record['mobileNumber']);
        self::assertSame('REGULAR', $record['membershipStatus']);
        self::assertFalse($record['hasSubscriptions']);
        self::assertArrayNotHasKey('roles', $record);
        self::assertArrayNotHasKey('isMember', $record);
        self::assertArrayNotHasKey('isSubscribed', $record);
    }

    private function user(string $uuid, array $roles, bool $isMember): User
    {
        $user = new User();
        $user->setUuid(Uuid::fromString($uuid));
        $user->setEmail(sprintf('%s@example.org', substr($uuid, -4)));
        $user->setRoles($roles);
        $user->setIsMember($isMember);
        $user->setIsShared(false);

        return $user;
    }
}
