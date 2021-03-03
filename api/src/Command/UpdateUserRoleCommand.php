<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\User;
use Symfony\Component\Console\Question\Question;

class UpdateUserRoleCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:add-role';
    private $entityManager;
    /** @var User $user */
    private $user;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Adds a role to a user.')
            ->setHelp('This command allows you to add to the roles of a user.')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the user.')
        ;
    }

// ...
    public function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Add Role to User',
            '================',
            '',
        ]);

        if($input->getArgument('email')) {
            $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $input->getArgument('email')]);
            if ($this->user) {
                return Command::SUCCESS;
            }
            else{
                $output->writeln("User with email address {$input->getArgument('email')} not found");
            }
        }

        $helper = $this->getHelper('question');
        $question = new Question("Please specify email address of user to update: ");

        $question->setValidator(function ($answer) {
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $answer]);
            if (!$user) {
                throw new \RuntimeException(
                    "User with email address {$answer} not found"
                );
            }

            return $answer;
        });
        $question->setMaxAttempts(2);

        $email = $helper->ask($input, $output, $question);

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        return Command::SUCCESS;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        // retrieve the argument value using getArgument()
        $output->writeln("User {$this->user->getUsername()}({$this->user->getEmail()}) has the following roles:");
        $roles = $this->user->getRoles();
        $output->writeln(implode("\n", $roles));

        $helper = $this->getHelper('question');
        $question = new Question("Please specify the role to add: ");

        $new_role = $helper->ask($input, $output, $question);
        if($new_role === null) {
            return Command::SUCCESS;
        }

        array_push($roles, $new_role);
        $this->user->setRoles($roles);
        $this->entityManager->flush();


        $output->writeln("User {$this->user->getUsername()}({$this->user->getEmail()}) now has the following roles:");
        $output->writeln(implode("\n", $roles));

        return Command::SUCCESS;
    }

}
