<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Entity\Page;
use App\Entity\User;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class InstallDevEnvCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:install:devenv';
    private $entityManager;
    private $validator;
    private $email;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates entities in the database for initial configuration.')
            ->setHelp('This command creates an administrator user in the database, along with rudamentary pages.')
            ->addArgument('email', InputArgument::OPTIONAL, 'The email of the administrator account.')
        ;
    }

    public function interact(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Generating Entities',
            '===================',
            '',
        ]);

        if($input->getArgument('email')) {
            return Command::SUCCESS;
        }

        $helper = $this->getHelper('question');
        $question = new Question("Please specify email address of administrator user: ");

        $question->setValidator(function ($answer) {

            // Validator kicks out an issue with recaptcha - need to resolve before re-enabling validation
            // $user = $this->createUser($answer);
            // $errors = $this->validator->validate($user);

            // if (count($errors) > 0) {
            //     /*
            //     * Uses a __toString method on the $errors variable which is a
            //     * ConstraintViolationList object. This gives us a nice string
            //     * for debugging.
            //     */
            //     $errorsString = (string) $errors;

            //     throw new \RuntimeException(
            //         $errorsString
            //     );
            // }

            // $this->user = $user;

            return $answer;
        });

        $question->setMaxAttempts(2);

        $this->email = $helper->ask($input, $output, $question);

        return Command::SUCCESS;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $user = $this->createUser($this->email);
        $this->entityManager->persist($user);
        $output->writeln("User created with email {$user->getEmail()})");

        $pages = $this->pages();
        foreach($pages as $page) {
            $this->entityManager->persist($page);
        }

        $output->writeln("Pages created");
        $this->entityManager->flush();

        
        return Command::SUCCESS;
    }

    private function createUser($email)
    {
        /** @var User $user */
        $user = new User();
        $user->setFirstName("Admin");
        $user->setLastName("Account");
        $user->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
        $user->setEmail($email);
        $user->setPassword("password");
        $user->setIsShared(false);

        return $user;
    }

    private function pages() {
        // Currently, the pages URLs and page IDs are hard coded - not ideal.  However, this creates the blank pages necessary
        $pages = [];

        $page = new Page();
        $page->setTitle("About");
        $page->setContent("About Page - some content goes here");
        $page->setIsPublished(true);     
        array_push($pages, $page);

        $page = new Page();
        $page->setTitle("Home");
        $page->setContent("Home Page - some content goes here");
        $page->setIsPublished(true);     
        array_push($pages, $page);

        $page = new Page();
        $page->setTitle("Terms and Conditions");
        $page->setContent("Terms and Conditions - some content goes here");
        $page->setIsPublished(true);     
        array_push($pages, $page);

        $page = new Page();
        $page->setTitle("Privacy");
        $page->setContent("Privacy - some content goes here");
        $page->setIsPublished(true);     
        array_push($pages, $page);
        
        return $pages;
    }

}
