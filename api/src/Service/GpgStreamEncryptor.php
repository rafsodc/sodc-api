<?php

namespace App\Service;

final class GpgStreamEncryptor
{
    private string $binary;

    public function __construct(string $binary = 'gpg')
    {
        $this->binary = $binary;
    }

    /** @param callable(callable(string): void): void $produce */
    public function encrypt(string $outputPath, string $recipient, callable $produce): void
    {
        if (1 !== preg_match('/\A(?:[A-Fa-f0-9]{40}|[A-Fa-f0-9]{64})\z/', $recipient)) {
            throw new \InvalidArgumentException('The GPG recipient must be a full 40- or 64-character fingerprint.');
        }
        if (file_exists($outputPath)) {
            throw new \RuntimeException(sprintf('Refusing to overwrite existing file: %s', $outputPath));
        }

        $command = [
            $this->binary,
            '--batch',
            '--no-tty',
            '--trust-model',
            'always',
            '--output',
            $outputPath,
            '--encrypt',
            '--recipient',
            $recipient,
        ];
        $pipes = [];
        $process = proc_open($command, [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (!is_resource($process)) {
            throw new \RuntimeException('Unable to start GPG.');
        }

        try {
            $produce(function (string $chunk) use ($pipes): void {
                $remaining = $chunk;
                while ('' !== $remaining) {
                    $written = fwrite($pipes[0], $remaining);
                    if (false === $written || 0 === $written) {
                        throw new \RuntimeException('GPG stopped accepting export data.');
                    }
                    $remaining = (string) substr($remaining, $written);
                }
            });
            fclose($pipes[0]);
            $stdout = stream_get_contents($pipes[1]);
            $stderr = stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            $exitCode = proc_close($process);
            $process = null;

            if (0 !== $exitCode) {
                throw new \RuntimeException(sprintf('GPG encryption failed%s%s', '' === trim((string) $stderr) ? '.' : ': ', trim((string) $stderr)));
            }
            if ('' !== trim((string) $stdout)) {
                throw new \RuntimeException('GPG unexpectedly emitted plaintext output.');
            }
            if (!is_file($outputPath)) {
                throw new \RuntimeException('GPG did not create the encrypted artifact.');
            }

            @chmod($outputPath, 0600);
        } catch (\Throwable $exception) {
            foreach ($pipes as $pipe) {
                if (is_resource($pipe)) {
                    fclose($pipe);
                }
            }
            if (is_resource($process)) {
                proc_terminate($process);
                proc_close($process);
            }
            if (is_file($outputPath)) {
                unlink($outputPath);
            }

            throw $exception;
        }
    }
}
