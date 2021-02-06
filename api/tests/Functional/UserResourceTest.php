<?php

namespace App\Tests\Functional;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use App\Entity\User;

class UserResourceTest extends CustomApiTestCase {

    use ReloadDatabaseTrait;

    public $email = 'test@test.com';
    public $pass = 'test';
    public $username = 'Test';
    public $phone = '01234567890';

    public function testUserCreate()
    {
        $this->setUp();
/*        $em = $this->getEntityManager();
        $users = $em->getRepository(User::class);
        $totalArticles = $users->createQueryBuilder('a')->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $this->assertEquals(0, $totalArticles);*/

        $client = self::createClient();

        // Test that a password is required
        $client->request('POST', '/users', [
            'json' => [
                'email' => $this->email,
                'username' => $this->username
            ]
        ]);
        $this->assertResponseStatusCodeSame(400);


        $client->request('POST', '/users', [
            'json' => [
                'email' => $this->email,
                'username' => $this->username,
                'password' => $this->pass
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->logIn($client, $this->email, $this->pass);
    }

    public function testUserRead()
    {
        $this->setUp();
        $client = self::createClient();
        $user = $this->createUser('user@test.com', 'test');

        // Check anon cannot access user
        $client->request('GET', "/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(401);

        // Create user and log in
        $me = $this->createUserAndLogIn($client, $this->email, $this->pass);
        // Check location header is correct
        $this->assertResponseHeaderSame('Location', "/users/{$me->getId()}");

        // Check access to user page
        $client->request('GET', "/users/{$user->getId()}");
        $this->assertResponseStatusCodeSame(200);

    }

    public function testUserUpdate()
    {
        $this->setUp();
        $client = self::createClient();
        $user = $this->createUser('user@test.com', 'test');
        $me = $this->createUserAndLogIn($client, $this->email, $this->pass);

        // Check user can update itself
        $client->request('PUT', "/users/{$me->getId()}", [
            'json' => ['username' => 'newusername']
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'username' => 'newusername'
        ]);

        // Check user cannot update another user
        $client->request('PUT', "/users/{$user->getId()}", [
            'json' => ['username' => 'Jack']
        ]);
        $this->assertResponseStatusCodeSame(403);
    }

    public function testUserGet()
    {
        $client = self::createClient();
        $user = $this->createUser('user@test.com', 'test');
        $me = $this->createUserAndLogIn($client, $this->email, $this->pass);
        $user->setPhoneNumber($this->phone);
        $em = $this->getEntityManager();
        $em->flush();
        $client->request('GET', '/users/'.$user->getId());
        $this->assertJsonContains([
            'email' => 'user@test.com'
        ]);
        $data = $client->getResponse()->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);
        // refresh the user & elevate
        // EntityManager is effectively reloaded after each client->request (effectively like a page load).  Whilst we still know $user,
        // EntityManager does not know it's managing it.  So we need to reload $user through EntityManager if we are to flush and persist data.
        $me = $em->getRepository(User::class)->find($me->getId());
        $me->setRoles(['ROLE_ADMIN']);
        $em->flush();
        $this->logIn($client, $this->email, $this->pass);
        $client->request('GET', '/users/'.$user->getId());
        $this->assertJsonContains([
            'phoneNumber' => $this->phone
        ]);
    }

    public function testIsMe()
    {
        $client = self::createClient();
        $user = $this->createUser('user@test.com', 'test');
        $me = $this->createUserAndLogIn($client, $this->email, $this->pass);
        $client->request('GET', '/users/'.$me->getId());
        $this->assertJsonContains([
            'isMe' => true
        ]);
        $client->request('GET', '/users/'.$user->getId());
        $this->assertJsonContains([
            'isMe' => false
        ]);
    }

}
