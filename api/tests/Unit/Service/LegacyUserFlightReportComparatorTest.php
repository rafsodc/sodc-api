<?php

namespace App\Tests\Unit\Service;

use App\Service\LegacyUserExportSchema;
use App\Service\LegacyUserFlightReportComparator;
use App\Service\LegacyUserSchemaCounter;
use PHPUnit\Framework\TestCase;

final class LegacyUserFlightReportComparatorTest extends TestCase
{
    public function testIgnoresWrapperMetadataAndMatchesCanonicalCounts(): void
    {
        $preflight = $this->report('preflight/v1', '2026-07-19T00:00:00Z');
        $postflight = $this->report('postflight/v1', '2026-07-20T00:00:00Z');

        $comparison = (new LegacyUserFlightReportComparator())->compare(
            $preflight,
            $postflight,
            new \DateTimeImmutable('2026-07-21T12:00:00+01:00')
        );

        self::assertTrue($comparison['matches']);
        self::assertSame([], $comparison['differences']);
        self::assertSame('2026-07-21T11:00:00Z', $comparison['comparedAt']);
    }

    public function testReportsExactCanonicalCountDifferences(): void
    {
        $preflight = $this->report('preflight/v1', '2026-07-19T00:00:00Z');
        $postflight = $this->report('postflight/v1', '2026-07-20T00:00:00Z');
        $preflight['overall']['fields']['hasSubscriptions']['true'] = 3;
        $postflight['overall']['fields']['hasSubscriptions']['true'] = 2;
        $postflight['byOldUid']['missing']['fields']['email']['present'] = 1;

        $comparison = (new LegacyUserFlightReportComparator())->compare(
            $preflight,
            $postflight,
            new \DateTimeImmutable('2026-07-21T12:00:00Z')
        );

        self::assertFalse($comparison['matches']);
        self::assertSame([
            [
                'path' => 'overall.fields.hasSubscriptions.true',
                'preflight' => 3,
                'postflight' => 2,
            ],
            [
                'path' => 'byOldUid.missing.fields.email.present',
                'preflight' => 0,
                'postflight' => 1,
            ],
        ], $comparison['differences']);
    }

    public function testComparesRecordSchemaVersion(): void
    {
        $preflight = $this->report('preflight/v1', '2026-07-19T00:00:00Z');
        $postflight = $this->report('postflight/v1', '2026-07-20T00:00:00Z');
        $postflight['recordSchemaVersion'] = 'different';

        $comparison = (new LegacyUserFlightReportComparator())->compare(
            $preflight,
            $postflight,
            new \DateTimeImmutable('2026-07-21T12:00:00Z')
        );

        self::assertFalse($comparison['matches']);
        self::assertSame('recordSchemaVersion', $comparison['differences'][0]['path']);
    }

    private function report(string $schemaVersion, string $generatedAt): array
    {
        $counter = new LegacyUserSchemaCounter();

        return [
            'schemaVersion' => $schemaVersion,
            'recordSchemaVersion' => LegacyUserExportSchema::RECORD_SCHEMA_VERSION,
            'generatedAt' => $generatedAt,
            'overall' => $counter->emptyCounts(),
            'byOldUid' => [
                'set' => $counter->emptyCounts(),
                'missing' => $counter->emptyCounts(),
            ],
        ];
    }
}
