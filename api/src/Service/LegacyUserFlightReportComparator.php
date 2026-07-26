<?php

namespace App\Service;

final class LegacyUserFlightReportComparator
{
    public function compare(array $preflight, array $postflight, \DateTimeImmutable $comparedAt): array
    {
        $this->assertReportShape($preflight, 'Preflight');
        $this->assertReportShape($postflight, 'Postflight');

        $expected = $this->comparableValues($preflight);
        $actual = $this->comparableValues($postflight);
        $differences = [];
        $this->collectDifferences($expected, $actual, '', $differences);

        return [
            'schemaVersion' => LegacyUserExportSchema::COMPARISON_SCHEMA_VERSION,
            'recordSchemaVersion' => $preflight['recordSchemaVersion'],
            'comparedAt' => $comparedAt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
            'matches' => [] === $differences,
            'differences' => $differences,
        ];
    }

    private function assertReportShape(array $report, string $label): void
    {
        foreach (['recordSchemaVersion', 'overall', 'byOldUid'] as $key) {
            if (!array_key_exists($key, $report)) {
                throw new \UnexpectedValueException(sprintf('%s report is missing %s.', $label, $key));
            }
        }
        if (!is_string($report['recordSchemaVersion']) || !is_array($report['overall']) || !is_array($report['byOldUid'])) {
            throw new \UnexpectedValueException(sprintf('%s report has an invalid structure.', $label));
        }
        foreach (['set', 'missing'] as $cohort) {
            if (!isset($report['byOldUid'][$cohort]) || !is_array($report['byOldUid'][$cohort])) {
                throw new \UnexpectedValueException(sprintf('%s report is missing the byOldUid.%s cohort.', $label, $cohort));
            }
        }
    }

    private function comparableValues(array $report): array
    {
        return [
            'recordSchemaVersion' => $report['recordSchemaVersion'],
            'overall' => $report['overall'],
            'byOldUid' => [
                'set' => $report['byOldUid']['set'],
                'missing' => $report['byOldUid']['missing'],
            ],
        ];
    }

    private function collectDifferences($expected, $actual, string $path, array & $differences): void
    {
        if (!is_array($expected) || !is_array($actual)) {
            if ($expected !== $actual) {
                $differences[] = ['path' => $path, 'preflight' => $expected, 'postflight' => $actual];
            }

            return;
        }

        $keys = array_values(array_unique(array_merge(array_keys($expected), array_keys($actual))));
        foreach ($keys as $key) {
            $childPath = '' === $path ? (string) $key : $path.'.'.$key;
            if (!array_key_exists($key, $expected)) {
                $differences[] = ['path' => $childPath, 'preflight' => '<missing>', 'postflight' => $actual[$key]];
                continue;
            }
            if (!array_key_exists($key, $actual)) {
                $differences[] = ['path' => $childPath, 'preflight' => $expected[$key], 'postflight' => '<missing>'];
                continue;
            }
            $this->collectDifferences($expected[$key], $actual[$key], $childPath, $differences);
        }
    }
}
