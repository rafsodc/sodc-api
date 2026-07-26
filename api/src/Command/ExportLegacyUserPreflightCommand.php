<?php

namespace App\Command;

use App\Service\LegacyUserExportSource;
use App\Service\LegacyUserPreflightBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ExportLegacyUserPreflightCommand extends Command
{
    protected static $defaultName = 'app:user:export-preflight';

    private LegacyUserExportSource $source;
    private LegacyUserPreflightBuilder $builder;

    public function __construct(LegacyUserExportSource $source, LegacyUserPreflightBuilder $builder)
    {
        parent::__construct();
        $this->source = $source;
        $this->builder = $builder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Writes a non-PII aggregate preflight report for the legacy user migration.')
            ->setHelp('The JSON report counts only the canonical export schema. It never emits user identifiers or source field values.')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Write the report to a new file instead of standard output.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $report = $this->builder->build($this->source->users(), new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
            $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)."\n";
            $outputPath = $input->getOption('output');
            if (null === $outputPath) {
                $output->write($json);
            } else {
                $this->writeNewFile((string) $outputPath, $json);
                $io->success(sprintf('Preflight report written to %s.', $outputPath));
            }
        } catch (\Throwable $exception) {
            $io->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function writeNewFile(string $path, string $contents): void
    {
        $handle = @fopen($path, 'x');
        if (false === $handle) {
            throw new \RuntimeException(sprintf('Unable to create report; the path may already exist: %s', $path));
        }

        try {
            if (strlen($contents) !== fwrite($handle, $contents)) {
                throw new \RuntimeException(sprintf('Unable to write complete report: %s', $path));
            }
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
