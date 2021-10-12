<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\User;
use Symfony\Component\Console\Question\Question;

class SetUserEmailToLowerCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:email-to-lower';
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
            ->setDescription('Changes all email addresses to lower case.')
            ->setHelp('This command allows you to set emails to lower case.')
        ;
    }


    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Setting emails to lower case',
            '================',
            '',
        ]);

        $users = $this->entityManager->getRepository(User::class)->findAll();

        foreach($users as $user) {
            $user->setEmail($user->getEmail());
        }

        // // retrieve the argument value using getArgument()
        // $output->writeln("User {$this->user->getUsername()}({$this->user->getEmail()}) has the following roles:");
        // $roles = $this->user->getRoles();
        // $output->writeln(implode("\n", $roles));

        // $helper = $this->getHelper('question');
        // $question = new Question("Please specify the role to add: ");

        // $new_role = $helper->ask($input, $output, $question);
        // if($new_role === null) {
        //     return Command::SUCCESS;
        // }

        // array_push($roles, $new_role);
        // $this->user->setRoles($roles);
        $this->entityManager->flush();


        // $output->writeln("User {$this->user->getUsername()}({$this->user->getEmail()}) now has the following roles:");
        // $output->writeln(implode("\n", $roles));

        return Command::SUCCESS;
    }

}
