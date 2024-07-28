<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\TicketType;
use App\Repository\EventRepository;
use App\Repository\TicketTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CopyTicketTypesCommand extends Command
{
    protected static $defaultName = 'app:event:copy-ticket-types';

    private $entityManager;
    private $eventRepository;
    private $ticketTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, EventRepository $eventRepository, TicketTypeRepository $ticketTypeRepository)
    {
        parent::__construct();
        
        $this->entityManager = $entityManager;
        $this->eventRepository = $eventRepository;
        $this->ticketTypeRepository = $ticketTypeRepository;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Copy TicketTypes from one Event to another')
            ->addArgument('sourceEventId', InputArgument::REQUIRED, 'ID of the source event')
            ->addArgument('targetEventId', InputArgument::REQUIRED, 'ID of the target event');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $sourceEventId = $input->getArgument('sourceEventId');
        $targetEventId = $input->getArgument('targetEventId');

        $sourceEvent = $this->eventRepository->find($sourceEventId);
        $targetEvent = $this->eventRepository->find($targetEventId);

        if (!$sourceEvent || !$targetEvent) {
            $io->error('Invalid source or target event ID.');
            return Command::FAILURE;
        }

        foreach ($sourceEvent->getTicketTypes() as $ticketType) {
            $newTicketType = new TicketType();
            $newTicketType->setUuid(Uuid::uuid4());
            $newTicketType->setDescription($ticketType->getDescription());
            $newTicketType->setSymposium($ticketType->getSymposium());
            $newTicketType->setDinner($ticketType->getDinner());
            $newTicketType->setServing($ticketType->getServing());
            $newTicketType->setStudent($ticketType->getStudent());
            $newTicketType->setGuest($ticketType->getGuest());
            $newTicketType->setPrice($ticketType->getPrice());
            $newTicketType->setEvent($targetEvent);

            $this->entityManager->persist($newTicketType);
        }

        $this->entityManager->flush();

        $io->success('Ticket types copied successfully.');

        return Command::SUCCESS;
    }
}
