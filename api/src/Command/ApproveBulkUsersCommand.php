<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\ArrayInput;
use App\Entity\User;
use App\Entity\Ticket;
use App\Entity\TicketType;
use App\Entity\Event;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Ramsey\Uuid\Uuid;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;

class ApproveBulkUsersCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:approve_bulk';
    private $entityManager;
    private $refreshTokenManager;
    /** @var User $user */
    private $users;
    private $delete;

    public function __construct(EntityManagerInterface $entityManager, RefreshTokenManagerInterface $refreshTokenManager)
    {
        $this->entityManager = $entityManager;
        $this->refreshTokenManager = $refreshTokenManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Approve new user accounts.')
            ->setHelp('This command allows you to approve users.')
        ;
    }

// ...
    public function interact(InputInterface $input, OutputInterface $output)
    {
        // Get all users from db :(
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $this->serving = [];
        $this->retired = [];
        $this->guest = [];
        $this->reject = [];
        
        $helper = $this->getHelper('question');

        foreach($users as $user) {
            if( $user->getRoles() === [] ){     
                $output->writeln(sprintf("%s, %s - %s - %s", $user->getLastName(), $user->getFirstName(), $user->getServiceNumber(), $user->getEmail()));
                $question = new ChoiceQuestion("Please select an option (defaults to skip)", ['skip', 'approve serving member', 'approve retired member', 'approve guest', 'reject'], 0);
                $question->setErrorMessage('Option %s is invalid');
                $response = $helper->ask($input, $output, $question);
                
                switch($response) {
                    case 'approve serving member':
                        array_push($this->serving, $user);
                        break;
                    case 'approve retired member':
                        array_push($this->retired, $user);
                        break;
                    case 'approve guest':
                        array_push($this->guest, $user);
                        break;
                    case 'reject':
                        array_push($this->reject, $user);
                        break;
                }
            }
        }
        return Command::SUCCESS;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('app:user:approve');
        
        foreach($this->serving as $user) {
            $arguments = [
                'uid' => $user->getId(),
                'serving' => 'Y',
                'retired' => 'N',
                'guest' => 'N',
                'delete' => 'N'
            ];
            $argInput = new ArrayInput($arguments);
            $returnCode = $command->run($argInput, $output);
        }

        foreach($this->retired as $user) {
            $arguments = [
                'uid' => $user->getId(),
                'serving' => 'N',
                'retired' => 'Y',
                'guest' => 'N',
                'delete' => 'N'
            ];
            $argInput = new ArrayInput($arguments);
            $returnCode = $command->run($argInput, $output);
        }

        foreach($this->guest as $user) {
            $arguments = [
                'uid' => $user->getId(),
                'serving' => 'N',
                'retired' => 'N',
                'guest' => 'Y',
                'delete' => 'N'
            ];
            $argInput = new ArrayInput($arguments);
            $returnCode = $command->run($argInput, $output);
        }

        foreach($this->reject as $user) {
            $arguments = [
                'uid' => $user->getId(),
                'serving' => 'N',
                'retired' => 'N',
                'guest' => 'N',
                'delete' => 'Y'
            ];
            $argInput = new ArrayInput($arguments);
            $returnCode = $command->run($argInput, $output);
        }

        return Command::SUCCESS;
    }

}
