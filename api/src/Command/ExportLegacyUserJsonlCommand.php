<?php

namespace App\Command;

use App\Service\GpgStreamEncryptor;
use App\Service\LegacyUserExportSchema;
use App\Service\LegacyUserExportSource;
use App\Service\LegacyUserJsonlExporter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

final class ExportLegacyUserJsonlCommand extends Command
{
    protected static $defaultName = 'app:user:export-jsonl';

    private LegacyUserExportSource $source;
    private LegacyUserJsonlExporter $exporter;
    private GpgStreamEncryptor $encryptor;
    private KernelInterface $kernel;

    public function __construct(
        LegacyUserExportSource $source,
        LegacyUserJsonlExporter $exporter,
        GpgStreamEncryptor $encryptor,
        KernelInterface $kernel
    ) {
        parent::__construct();
        $this->source = $source;
        $this->exporter = $exporter;
        $this->encryptor = $encryptor;
        $this->kernel = $kernel;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Streams the canonical legacy-user JSONL export directly into GPG encryption.')
            ->setHelp('The command never writes plaintext JSONL. The encrypted artifact and its manifest must be created outside this repository.')
            ->addArgument('output', InputArgument::REQUIRED, 'New encrypted output path, normally ending .jsonl.gpg.')
            ->addOption('recipient', 'r', InputOption::VALUE_REQUIRED, 'Full 40- or 64-character GPG recipient fingerprint.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $outputPath = $this->resolveOutputPath((string) $input->getArgument('output'));
            $recipient = (string) $input->getOption('recipient');
            if ('' === trim($recipient)) {
                throw new \InvalidArgumentException('Use --recipient with the intended full GPG key fingerprint.');
            }

            $manifestPath = $outputPath.'.manifest.json';
            if (file_exists($manifestPath)) {
                throw new \RuntimeException(sprintf('Refusing to overwrite existing manifest: %s', $manifestPath));
            }

            $counts = null;
            $this->encryptor->encrypt(
                $outputPath,
                $recipient,
                function (callable $write) use (&$counts): void {
                    $counts = $this->exporter->export($this->source->users(), $write);
                }
            );

            if (!is_array($counts)) {
                throw new \RuntimeException('The export did not produce canonical counts.');
            }

            try {
                $encryptedSha256 = hash_file('sha256', $outputPath);
                if (false === $encryptedSha256) {
                    throw new \RuntimeException('Unable to checksum the encrypted artifact.');
                }
                $manifest = [
                    'schemaVersion' => LegacyUserExportSchema::MANIFEST_SCHEMA_VERSION,
                    'recordSchemaVersion' => LegacyUserExportSchema::RECORD_SCHEMA_VERSION,
                    'generatedAt' => (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->format('Y-m-d\TH:i:s\Z'),
                    'recordCount' => $counts['recordCount'],
                    'encryptedArtifact' => basename($outputPath),
                    'encryptedSha256' => $encryptedSha256,
                ];
                $this->writeManifest($manifestPath, $manifest);
            } catch (\Throwable $exception) {
                if (is_file($outputPath)) {
                    unlink($outputPath);
                }
                throw $exception;
            }

            $io->success(sprintf(
                'Encrypted %d canonical user records to %s. Manifest: %s',
                $counts['recordCount'],
                $outputPath,
                $manifestPath
            ));

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }
    }

    private function resolveOutputPath(string $path): string
    {
        if ('' === trim($path)) {
            throw new \InvalidArgumentException('An encrypted output path is required.');
        }

        $directory = realpath(dirname($path));
        if (false === $directory || !is_dir($directory) || !is_writable($directory)) {
            throw new \RuntimeException('The encrypted output directory must already exist and be writable.');
        }

        $outputPath = $directory.DIRECTORY_SEPARATOR.basename($path);
        $projectDirectory = realpath($this->kernel->getProjectDir());
        $projectParent = realpath(dirname($this->kernel->getProjectDir()));
        $repositoryRoot = false !== $projectParent && is_dir($projectParent.DIRECTORY_SEPARATOR.'.git')
            ? $projectParent
            : $projectDirectory;
        if (false !== $repositoryRoot && ($outputPath === $repositoryRoot || 0 === strpos($outputPath, $repositoryRoot.DIRECTORY_SEPARATOR))) {
            throw new \RuntimeException('The encrypted migration artifact must be written outside the repository.');
        }

        return $outputPath;
    }

    private function writeManifest(string $path, array $manifest): void
    {
        $contents = json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
        $handle = @fopen($path, 'x');
        if (false === $handle) {
            throw new \RuntimeException(sprintf('Unable to create export manifest: %s', $path));
        }

        try {
            if (strlen($contents) !== fwrite($handle, $contents)) {
                throw new \RuntimeException(sprintf('Unable to write complete export manifest: %s', $path));
            }
            @chmod($path, 0600);
        } catch (\Throwable $exception) {
            fclose($handle);
            if (is_file($path)) {
                unlink($path);
            }
            throw $exception;
        } finally {
            if (is_resource($handle)) {
                fclose($handle);
            }
        }
    }
}
