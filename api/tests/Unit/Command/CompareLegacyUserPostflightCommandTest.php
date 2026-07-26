<?php

namespace App\Tests\Unit\Command;

use App\Command\CompareLegacyUserPostflightCommand;
use App\Service\LegacyUserExportSchema;
use App\Service\LegacyUserFlightReportComparator;
use App\Service\LegacyUserSchemaCounter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CompareLegacyUserPostflightCommandTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/sodc-postflight-'.bin2hex(random_bytes(6));
        mkdir($this->directory, 0700);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory.'/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    public function testReturnsSuccessForMatchingReports(): void
    {
        [$preflight, $postflight] = $this->writeReports(false);
        $tester = new CommandTester(new CompareLegacyUserPostflightCommand(new LegacyUserFlightReportComparator()));

        $status = $tester->execute(['preflight' => $preflight, 'postflight' => $postflight]);

        self::assertSame(Command::SUCCESS, $status);
        self::assertTrue(json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR)['matches']);
    }

    public function testReturnsFailureAndDifferencesForMismatchingReports(): void
    {
        [$preflight, $postflight] = $this->writeReports(true);
        $tester = new CommandTester(new CompareLegacyUserPostflightCommand(new LegacyUserFlightReportComparator()));

        $status = $tester->execute(['preflight' => $preflight, 'postflight' => $postflight]);
        $comparison = json_decode($tester->getDisplay(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(Command::FAILURE, $status);
        self::assertFalse($comparison['matches']);
        self::assertSame('overall.recordCount', $comparison['differences'][0]['path']);
    }

    private function writeReports(bool $mismatch): array
    {
        $counter = new LegacyUserSchemaCounter();
        $report = [
            'schemaVersion' => 'wrapper/v1',
            'recordSchemaVersion' => LegacyUserExportSchema::RECORD_SCHEMA_VERSION,
            'generatedAt' => '2026-07-21T00:00:00Z',
            'overall' => $counter->emptyCounts(),
            'byOldUid' => [
                'set' => $counter->emptyCounts(),
                'missing' => $counter->emptyCounts(),
            ],
        ];
        $postflight = $report;
        $postflight['schemaVersion'] = 'another-wrapper/v1';
        $postflight['generatedAt'] = '2026-07-22T00:00:00Z';
        if ($mismatch) {
            $postflight['overall']['recordCount'] = 1;
        }

        $preflightPath = $this->directory.'/preflight.json';
        $postflightPath = $this->directory.'/postflight.json';
        file_put_contents($preflightPath, json_encode($report, JSON_THROW_ON_ERROR));
        file_put_contents($postflightPath, json_encode($postflight, JSON_THROW_ON_ERROR));

        return [$preflightPath, $postflightPath];
    }
}
