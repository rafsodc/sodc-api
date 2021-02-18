<?php

namespace App\Test;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;
use App\Tests\Functional\UserResourceTest;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Exception;

class CustomApiTestCase extends ApiTestCase
{
    public $authedUser;

    static function setUpBeforeClass(): void {
        self::bootKernel();
        self::purgeDatabase();
        UserResourceTest::generateUsers();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->loadTestUser();
    }

    public function loadTestUser(): void
    {
        $userData = new CsvFileIterator('tests/data/initUserData.csv');
        $userData->next();

        if($userData->valid()) {
            $user = [
                'email' => $userData->current()[0],
                'username' => $userData->current()[1],
                'password' => $userData->current()[2],
                'phoneNumber' => $userData->current()[3]
            ];

        }

        $this->authedUser = $user;
    }

    protected function setUserRoles($client, $user, $roles) {
        // EntityManager is effectively reloaded after each client->request (effectively like a page load).  Whilst we still know $user,
        // EntityManager does not know it's managing it.  So we need to reload $user through EntityManager if we are to flush and persist data.
        $user = $this->getEntityManager()->getRepository(User::class)->find($user->getId());
        $user->setRoles($roles);
        $this->getEntityManager()->flush();
        $this->logIn($client, $this->authedUser['email'], $this->authedUser['password']);
    }

    protected function logIn(Client $client, string $email, string $password)
    {
        $client->request('POST', '/login', [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => [
                'email' => $email,
                'password' => $password
            ],
        ]);
        $this->assertResponseStatusCodeSame(204);
    }

    public function createAuthedClient() {

        $client = self::createClient();
        $this->logIn($client, $this->authedUser['email'], $this->authedUser['password']);
        return $client;
    }

    static function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    static function purgeDatabase()
    {
        $em = self::$container->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $purger->purge();
    }

    protected function checkResponse(ResponseInterface $response, array $requiredFields) {
        try {
            $response->getContent();
            $this->assertTrue(false, "No exception raised for invalid data");
        }
        catch (Exception $exception) {
            foreach($requiredFields as $field) {
                // Structure of error message is "$field: This value..."
                // eg. email: This value is already used.
                // email: This value should not be blank.
                $this->assertStringContainsString("$field:", $exception->getMessage());
            }
        }
    }
}
