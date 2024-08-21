<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\User;
use App\Entity\Ticket;
use App\Entity\TicketType;
use App\Entity\Event;
use App\Message\UserApprove;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Question\Question;
use Ramsey\Uuid\Uuid;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use App\Service\SubscriptionService;

class ApproveUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:approve';
    private $entityManager;
    private $refreshTokenManager;
    /** @var User $user */
    private $user;
    private $messageBus;
    private $subscriptionService;

    public function __construct(EntityManagerInterface $entityManager, MessageBusInterface $messageBus, SubscriptionService $subscriptionService) //RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
        $this->subscriptionService = $subscriptionService;
        //$this->refreshTokenManager = $refreshTokenManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Approve user account.')
            ->setHelp('This command allows you to approve users.')
            ->addArgument('uid', InputArgument::OPTIONAL, 'User ID.')
            ->addArgument('serving', InputArgument::OPTIONAL, 'Serving')
            ->addArgument('retired', InputArgument::OPTIONAL, 'Retired')
            ->addArgument('guest', InputArgument::OPTIONAL, 'Guest')
            ->addArgument('delete', InputArgument::OPTIONAL, 'Delete')
        ;
    }

// ...
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $this->user = $this->entityManager->getRepository(User::class)->find($input->getArgument('uid'));
        if ($this->user) {
            return Command::SUCCESS;
        }
        else{
            $output->writeln("User not found");
        }
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $roles = [];

        if($input->getArgument('serving') === 'Y') {
            array_push($roles, 'ROLE_USER');
            array_push($roles, 'ROLE_MEMBER');
            array_push($roles, 'ROLE_SERVING');
        }
        else if($input->getArgument('retired') === 'Y') {
            array_push($roles, 'ROLE_USER');
            array_push($roles, 'ROLE_MEMBER');
            array_push($roles, 'ROLE_RETIRED');
        }
        else if($input->getArgument('guest') === 'Y') {
            array_push($roles, 'ROLE_USER');
            array_push($roles, 'ROLE_GUEST');
            array_push($roles, 'ROLE_RETIRED');
        }
        else if($input->getArgument('delete') === 'Y') {
            array_push($roles, 'ROLE_DELETED');
        }

        $this->user->setRoles($roles);

        // Assign default subscriptions based on the user's roles
        $this->subscriptionService->setDefaultSubscriptions($this->user);
        
        $this->entityManager->flush();

        if($input->getArgument('delete') !== 'Y') {
            $message = new UserApprove($this->user->getId());
            $this->messageBus->dispatch($message);
        }
        return Command::SUCCESS;
    }

}
