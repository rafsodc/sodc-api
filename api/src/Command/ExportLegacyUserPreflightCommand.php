<?php

namespace App\Command;

use App\Entity\Subscription;
use App\Entity\User;
use App\Service\LegacyUserPreflightBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ExportLegacyUserPreflightCommand extends Command
{
    protected static $defaultName = 'app:user:export-preflight';

    private EntityManagerInterface $entityManager;
    private LegacyUserPreflightBuilder $builder;

    public function __construct(EntityManagerInterface $entityManager, LegacyUserPreflightBuilder $builder)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->builder = $builder;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Writes a non-PII aggregate preflight report for the legacy user migration.')
            ->setHelp('The JSON report contains counts and approved catalogue values only. It never emits user identifiers or source field values.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->entityManager->createQueryBuilder()
            ->select('user', 'rank')
            ->from(User::class, 'user')
            ->leftJoin('user.rank', 'rank')
            ->orderBy('user.uuid', 'ASC')
            ->getQuery()
            ->toIterable();

        $subscriptions = $this->entityManager->getRepository(Subscription::class)->findBy([], ['uuid' => 'ASC']);
        $report = $this->builder->build($users, $subscriptions, new \DateTimeImmutable('now', new \DateTimeZone('UTC')));
        $json = json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $output->writeln($json);

        return Command::SUCCESS;
    }
}
