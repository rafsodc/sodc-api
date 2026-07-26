<?php

namespace App\Tests\Unit\Service;

use App\Service\GpgStreamEncryptor;
use PHPUnit\Framework\TestCase;

final class GpgStreamEncryptorTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/sodc-gpg-stream-'.bin2hex(random_bytes(6));
        mkdir($this->directory, 0700);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory.'/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->directory);
    }

    public function testStreamsProducerOutputToEncryptionProcess(): void
    {
        $binary = $this->directory.'/fake-gpg';
        file_put_contents($binary, <<<'PHP'
#!/usr/bin/env php
<?php
$outputIndex = array_search('--output', $argv, true);
$output = $argv[$outputIndex + 1];
file_put_contents($output, strrev(stream_get_contents(STDIN)));
PHP
        );
        chmod($binary, 0700);
        $output = $this->directory.'/users.jsonl.gpg';

        (new GpgStreamEncryptor($binary))->encrypt($output, str_repeat('A', 40), static function (callable $write): void {
            $write("first\n");
            $write("second\n");
        });

        self::assertSame(strrev("first\nsecond\n"), file_get_contents($output));
        clearstatcache(true, $output);
        self::assertSame(0600, fileperms($output) & 0777);
    }

    public function testRefusesToOverwriteExistingArtifact(): void
    {
        $output = $this->directory.'/existing.gpg';
        file_put_contents($output, 'existing');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Refusing to overwrite');

        (new GpgStreamEncryptor('/not/invoked'))->encrypt($output, str_repeat('A', 40), static function (): void {
        });
    }

    public function testRejectsShortRecipientKeyIds(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('full 40- or 64-character fingerprint');

        (new GpgStreamEncryptor('/not/invoked'))->encrypt($this->directory.'/users.gpg', 'ABC123', static function (): void {
        });
    }
}
