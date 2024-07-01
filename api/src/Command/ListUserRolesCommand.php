<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ListUserRolesCommand extends Command
{
    protected static $defaultName = 'app:user:roles';
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Lists all users with their roles in separate columns');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Fetch all users
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        // Collect all unique roles
        $allRoles = [];
        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                if (!in_array($role, $allRoles)) {
                    $allRoles[] = $role;
                }
            }
        }
        sort($allRoles);

        // Prepare the data for the table
        $headers = array_merge(['Name (Email)'], $allRoles);
        $table = new Table($output);
        $table->setHeaders($headers);

        foreach ($users as $user) {
            $name = sprintf('%s, %s (%s)', $user->getLastName(), $user->getFirstName(), $user->getEmail());
            $row = [$name];
            foreach ($allRoles as $role) {
                $row[] = in_array($role, $user->getRoles()) ? 'X' : '';
            }
            $table->addRow($row);
        }

        // Render the table
        $table->render();

        return Command::SUCCESS;
    }
}
