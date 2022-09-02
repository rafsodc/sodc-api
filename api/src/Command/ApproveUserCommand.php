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
use Symfony\Component\Console\Question\Question;
use Ramsey\Uuid\Uuid;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class ApproveUserCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:approve';
    private $entityManager;
    private $refreshTokenManager;
    /** @var User $user */
    private $user;

    public function __construct(EntityManagerInterface $entityManager, RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->refreshTokenManager = $refreshTokenManager;

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
        

        // $ticket = new Ticket;
        // $ticket->setTicketType( $this->entityManager->getRepository(TicketType::class)->find(15) );
        // $ticket->setUuid(Uuid::uuid4());
        // $ticket->setOwner($this->user);
        // $ticket->setEvent( $this->entityManager->getRepository(Event::class)->find(1) );

        // $ticket->setRank("N/A");
        // $ticket->setFirstname("N/A");
        // $ticket->setLastname("N/A");
        // $ticket->setDietary("N/A");
        // $this->entityManager->persist($ticket);
        
        $this->entityManager->flush();

        return Command::SUCCESS;
    }

}
