<?php

namespace App\Service;

use App\Entity\User;

final class LegacyUserPreflightBuilder
{
    public const SCHEMA_VERSION = LegacyUserExportSchema::PREFLIGHT_SCHEMA_VERSION;
    public const RECORD_SCHEMA_VERSION = LegacyUserExportSchema::RECORD_SCHEMA_VERSION;

    private LegacyUserRecordTransformer $transformer;
    private LegacyUserSchemaCounter $counter;

    public function __construct(LegacyUserRecordTransformer $transformer, LegacyUserSchemaCounter $counter)
    {
        $this->transformer = $transformer;
        $this->counter = $counter;
    }

    /** @param iterable<User> $users */
    public function build(iterable $users, \DateTimeImmutable $generatedAt): array
    {
        $report = [
            'schemaVersion' => self::SCHEMA_VERSION,
            'recordSchemaVersion' => self::RECORD_SCHEMA_VERSION,
            'generatedAt' => $generatedAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'overall' => $this->counter->emptyCounts(),
            'byOldUid' => [
                'set' => $this->counter->emptyCounts(),
                'missing' => $this->counter->emptyCounts(),
            ],
        ];

        foreach ($users as $user) {
            $record = $this->transformer->transform($user);
            if (null === $record) {
                continue;
            }

            $this->counter->count($record, $report['overall']);
            $cohort = null === $record['oldUid'] ? 'missing' : 'set';
            $this->counter->count($record, $report['byOldUid'][$cohort]);
        }

        return $report;
    }
}
