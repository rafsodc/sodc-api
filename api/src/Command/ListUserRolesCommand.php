<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
            ->setDescription('Lists all users with their roles in separate columns')
            ->addArgument('roles', InputArgument::IS_ARRAY, 'Roles to display (separate multiple roles with a space)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Fetch all users
        $userRepository = $this->entityManager->getRepository(User::class);
        $users = $userRepository->findAll();

        $total_users = count($users);

        // Get the roles argument
        $rolesArgument = $input->getArgument('roles');

        $count = ['Total'];

        // Collect all unique roles or use the provided roles argument
        $allRoles = !empty($rolesArgument) ? $rolesArgument : [];
        if (empty($rolesArgument)) {
            foreach ($users as $user) {
                foreach ($user->getRoles() as $role) {
                    if (!in_array($role, $allRoles)) {
                        $allRoles[] = $role;
                        $count[$role] = 0;
                    }
                }
            }
            sort($allRoles);
        }

        // Prepare the data for the table
        $headers = array_merge(['Name (Email)'], $allRoles);
        $table = new Table($output);
        $table->setHeaders($headers);        

        foreach ($users as $user) {
            // Check if the user has any of the specified roles with exclusions
            if (!$user->hasAnyRoleWithExclusions($allRoles)) {
                continue;
            }

            $name = sprintf('%s, %s (%s)', $user->getLastName(), $user->getFirstName(), $user->getEmail());
            $row = [$name];
            foreach ($allRoles as $role) {
                $row[] = in_array($role, $user->getRoles()) ? 'X' : '';
                $count[$role] = in_array($role, $user->getRoles()) ? $count[$role] + 1 : $count[$role];
            }
            $table->addRow($row);
        }

        $table->addRow($count);

        // Render the table
        $table->render();
        print("Total users: $total_users\n");

        return Command::SUCCESS;
    }
}
