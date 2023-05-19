<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\DataPersister\UserDataPersister;
use App\Entity\User;
use App\Entity\Rank;
use App\Repository\UserRepository;
use App\Repository\RankRepository;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Finder\Finder;

class GetUserEmails extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:get-user-emails';
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
            ->setDescription('Retrieves a list of all user emails and writes to a csv file.')
            ->setHelp('This command qrites all user emails to a csv file - useful for corresponding with all members.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
//        $output->writeln([
//            'Writing emails to data/user_emails.csv',
//            '================',
//            '',
//        ]);

        $users = $this->entityManager->getRepository(User::class)->findAll();

        $user_emails = [];
        foreach($users as $user) {
            if(in_array('ROLE_MEMBER', $user->getRoles()) or in_array('ROLE_SERVING', $user->getRoles()) or in_array('ROLE_RETIRED', $user->getRoles()))
            $user_emails[] = $user->getEmail();
        }

        $output->writeln([implode(';', $user_emails)]);

        return Command::SUCCESS;
    }

}
