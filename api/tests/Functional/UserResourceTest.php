<?php

namespace App\Tests\Functional;
use App\Test\CustomApiTestCase;
use App\Entity\User;
use App\Test\CsvFileIterator;

class UserResourceTest extends CustomApiTestCase {

    private $requiredFields = [
        'email',
        'password',
        'username'
    ];
    private $uniqueFields = [
        'email',
        'username'
    ];

    public static function generateUsers(): void
    {
        $userData = new CsvFileIterator('tests/data/initUserData.csv');
        $userData->next();

        while($userData->valid()) {
            self::createUser($userData->current()[0], $userData->current()[1], $userData->current()[2], $userData->current()[3]);
            $userData->next();
        }
    }

    protected static function createUser(string $email, string $username, string $password, string $phoneNumber = ''): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $user->setPhoneNumber($phoneNumber);

        $encoded = self::$container->get('security.password_encoder')
            ->encodePassword($user, $password);
        $user->setPassword($encoded);

        $em = self::getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }

    public function testDbUserTable() {
        $em = $this->getEntityManager();
        $userData = new CsvFileIterator('tests/data/initUserData.csv');
        $userData->next();
        $this->assertTrue($userData->valid(), "User data file has no entries.  Users not loaded into database.");

        while($userData->valid()) {
            $userCount = $em->getRepository(User::class)->count([
                'email' => $userData->current()[0],
                'username' => $userData->current()[1],
                'phoneNumber' => $userData->current()[3]
            ]);
            $this->assertEquals(1, $userCount);
            $userData->next();
        }
    }

    public function userProvider(): CsvFileIterator {
        return new CsvFileIterator('tests/data/testUserData.csv');
    }

    /**
     * @depends testDbUserTable
     * @dataProvider userProvider
     */
    public function testUserCreate($email, $username, $password, $phoneNumber): string
    {
        $client = self::createClient();
        $newUser = [
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'phoneNumber' => $phoneNumber
        ];

        // Test that a password is required
        $response = $client->request('POST', '/users', [
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(422);
        $this->checkResponse($response, $this->requiredFields);

        // Test a user can be created
        $response = $client->request('POST', '/users', [
            'json' => $newUser
        ]);

        $this->assertResponseStatusCodeSame(201);
        $location = $response->getHeaders()['location'][0];

        // Test a user cannot be created twice
        $request = $client->request('POST', '/users', [
            'json' => $newUser
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->checkResponse($request, $this->uniqueFields);

        return $location;
    }

    public function userAccess($client, $role, $id = 0)
    {
        /** @var User[] $users */
        $users = $this->getEntityManager()->getRepository(User::class)->findAll();

        foreach($users as $user) {
            switch($role) {
                case 'IS_AUTHENTICATED_ANONYMOUSLY':
                    $getCollection = 401;
                    $getItem = 401;
                    $putItem = 401;
                    break;
                case 'ROLE_USER':
                case 'ROLE_ADMIN':
                    $getCollection = 200;
                    $getItem = 200;
                    $putItem = ($user->getId() === $id || $role === 'ROLE_ADMIN') ? 200: 403;
                    break;
            }

            $path = $this->findIriBy(User::class, ['id' => $user->getId()]);

            // Check user list access
            $client->request('GET', "/users");
            $this->assertResponseStatusCodeSame($getCollection);

            // Check user access
            $client->request('GET', $path);
            $this->assertResponseStatusCodeSame($getItem);

            // Check user write access - change phone number by adding '1' at the end
            $client->request('PUT', $path, [
                'json' => ['phoneNumber' => "{$user->getPhoneNumber()}1" ]
            ]);
            $this->assertResponseStatusCodeSame($putItem);

        }
    }

    /**
     * Test access for normal user
     * @depends testUserCreate
     */
    public function testUserAccess()
    {
        $client = self::createClient();

        // Check anon access
        $this->userAccess($client, 'IS_AUTHENTICATED_ANONYMOUSLY');

        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $this->authedUser['email']]);
        $client = $this->createAuthedClient();
        // Check user access
        $this->userAccess($client, 'ROLE_USER', $user->getId());

        // Check admin access
        $this->setUserRoles($client, $user, ['ROLE_ADMIN']);
        $this->userAccess($client, 'ROLE_ADMIN', $user->getId());
    }
}
