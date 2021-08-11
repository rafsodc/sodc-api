<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
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

class LoadUsersFromCsvCommand extends Command
{
    // change these options about the file to read
    private $csvParsingOptions = array(
      'finder_in' => 'resources/',
      'finder_name' => 'users.csv'
    );

    private $keyToIndex = array(
        'uid' => 0,
        'username' => 1,
        'firstname' => 2,
        'lastname' => 3,
        'email' => 4,
        'rank' => 5,
        'postnoms' => 6,
        'servicenumber' => 7,
        'telephone' => 8,
        'mobile' => 9,
        'share' => 10,
        'authorised' => 11,
        'paid' => 12,
        'retired' => 13,
        'lost' => 14,
        'resigned' => 15,
        'deceased' => 16,
        'guest' => 17,
        'guesturl' => 18,
        'workdetails' => 19
    );

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:user:load';
    private $entityManager;
    private $userRepository;
    private $rankRepository;
    private $userManager;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, RankRepository $rankRepository, UserDataPersister $userManager)
    {
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->rankRepository = $rankRepository;
        $this->userManager = $userManager;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Loads users from a CSV file.')
            ->setHelp('This command allows you to import users.')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {

        $rows = $this->loadFile();

        foreach($rows as $row) {
            $this->processRow($row);
        }

        return Command::SUCCESS;
    }

    function processRow($data)
    {
        $user = $this->userRepository->findOneBy(['oldUid' => $data[$this->keyToIndex['uid']]]);
        
        if($user !== null) {
            return;
        }

        // If email address is not valid
        if($this->keyToIndex['email'] === '\N' || $this->keyToIndex['email'] === '' || $this->keyToIndex['email'] === 'secretary@sodc.net') {
            return;
        }
        

        $roles = [];

        // If this is a guest account, don't store
        if($data[$this->keyToIndex['guest']] === '0' || $data[$this->keyToIndex['guest']] === '1') {
            return;
        }

        // If the account hasn't been paid, don't store
        if($data[$this->keyToIndex['paid']] === '0') {
            return;
        }

        // Set up Roles
        // If deceased, only provide that role
        if($data[$this->keyToIndex['deceased']] === '1') {
            array_push($roles, 'ROLE_DECEASED');
        }

        // Otherwise, if they are resigned, give them that role and retired or serving roles
        else if($data[$this->keyToIndex['resigned']] === '1') {
            array_push($roles, 'ROLE_RESIGNED');

            if($data[$this->keyToIndex['retired']] === '1') {
                array_push($roles, 'ROLE_RETIRED');
            }

            if($data[$this->keyToIndex['retired']] === '2') {
                array_push($roles, 'ROLE_SERVING');
            }
        }

        // Otherise, give them that role, along with a user role (for logging in) and retired or serving roles
        else {
            array_push($roles, 'ROLE_USER');

            if($data[$this->keyToIndex['retired']] === '1') {
                array_push($roles, 'ROLE_RETIRED');
            }

            if($data[$this->keyToIndex['retired']] === '2') {
                array_push($roles, 'ROLE_SERVING');
            }

            // And the lost role if applicable
            if($data[$this->keyToIndex['lost']] === '1') {
                array_push($roles, 'ROLE_LOST');
            }
        }

        $rank = $this->rankRepository->findOneBy(['rank' => $data[$this->keyToIndex['rank']]]);
        if($rank === null) {
            $rank = new Rank;
            $rank->setRank($data[$this->keyToIndex['rank']]);
            $this->entityManager->persist($rank);
            //$this->entityManager->flush();
        }
        //dd($rank);

        //dd($roles);
        $user = new User;
        $user->setOldUid($data[$this->keyToIndex['uid']]);
        $user->setUsername($data[$this->keyToIndex['username']]);
        $user->setFirstName($data[$this->keyToIndex['firstname']]);
        $user->setLastName($data[$this->keyToIndex['lastname']]);
        $user->setEmail($data[$this->keyToIndex['email']]);
        $user->setPostNominals($data[$this->keyToIndex['postnoms']]);
        $user->setServiceNumber($data[$this->keyToIndex['servicenumber']]);
        $user->setPhoneNumber($data[$this->keyToIndex['telephone']]);
        $user->setMobileNumber($data[$this->keyToIndex['mobile']]);
        $user->setIsShared($data[$this->keyToIndex['share']] === '1');
        $user->setWorkDetails($data[$this->keyToIndex['workdetails']]);
        $user->setPassword("");
        $user->setRoles($roles);
        $user->setRank($rank);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
        
    }

    function loadFile() {
        $finder = new Finder();
        $finder->files()
            ->in($this->csvParsingOptions['finder_in'])
            ->name($this->csvParsingOptions['finder_name'])
        ;
        foreach ($finder as $file) { $csv = $file; }
        
        $rows = array();

        if (($handle = fopen($csv->getRealPath(), "r")) !== FALSE) {
            $i = 0;
            while (($data = fgetcsv($handle, null, ",")) !== FALSE) {
                $i++;
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }

}
