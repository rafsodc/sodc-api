<?php

namespace App\Tests\Functional;
use App\Entity\Event;
use App\Test\CustomApiTestCase;
use App\Entity\User;
use App\Test\CsvFileIterator;
use DateTime;

class EventResourceTest extends CustomApiTestCase {

    private $requiredFields = [
        'title',
        'date',
        'venue',
        'bookingOpen',
        'bookingClose'
    ];
    private $uniqueFields = [
    ];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::generateEvents();
    }

    public static function generateEvents(): void
    {
        $em = self::getEntityManager();
        $eventData = new CsvFileIterator('tests/data/initEventData.csv');
        $eventData->next();

        while($eventData->valid()) {
            $event = new Event();
            $event->setTitle($eventData->current()[0]);
            $event->setDate(DateTime::createFromFormat('j-M-Y', $eventData->current()[1]));
            $event->setVenue($eventData->current()[2]);
            $event->setBookingOpen(DateTime::createFromFormat('j-M-Y', $eventData->current()[3]));
            $event->setBookingClose(DateTime::createFromFormat('j-M-Y', $eventData->current()[4]));
            $em->persist($event);
            $em->flush();
            $eventData->next();
        }
    }

    public function testDbEventTable() {
        $em = $this->getEntityManager();
        $eventData = new CsvFileIterator('tests/data/initEventData.csv');
        $eventData->next();
        $this->assertTrue($eventData->valid(), "Event data file has no entries.  Users not loaded into database.");

        while($eventData->valid()) {
            $eventCount = $em->getRepository(Event::class)->count([
                'title' => $eventData->current()[0],
                'date' => DateTime::createFromFormat('j-M-Y', $eventData->current()[1]),
                'venue' => $eventData->current()[2],
                'bookingOpen' => DateTime::createFromFormat('j-M-Y',$eventData->current()[3]),
                'bookingClose' => DateTime::createFromFormat('j-M-Y',$eventData->current()[4]),
            ]);
            $this->assertEquals(1, $eventCount);
            $eventData->next();
        }
    }

    public function eventProvider(): CsvFileIterator {
        return new CsvFileIterator('tests/data/testEventData.csv');
    }

    /**
     * @depends testDbEventTable
     * @dataProvider eventProvider
     */
    public function testEventCreate($title, $date, $venue, $bookingOpen, $bookingClose): void
    {
        $client = $this->createAuthedClient();
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $this->authedUser['email']]);
        $this->setUserRoles($client, $user, ['ROLE_ADMIN']);

        $newEvent = [
            'title' => $title,
            'date' => $date,
            'venue' => $venue,
            'bookingOpen' => $bookingOpen,
            'bookingClose' => $bookingClose,
        ];

        // Test required fields
        $response = $client->request('POST', '/events', [
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(400);
        $this->checkResponse($response, $this->requiredFields);

        // Test an event can be created
        $response = $client->request('POST', '/events', [
            'json' => $newEvent
        ]);

        $this->assertResponseStatusCodeSame(201);

        // No unique fields on this one
        // Test an event cannot be created twice
        /*$request = $client->request('POST', '/events', [
            'json' => $this->testUser
        ]);

        $this->assertResponseStatusCodeSame(400);
        $this->checkResponse($request, $this->uniqueFields);*/
    }

    public function eventAccess($client, $role)
    {
        /** @var Event[] $events */
        $events = $this->getEntityManager()->getRepository(Event::class)->findAll();


        foreach($events as $event) {
            switch($role) {
                case 'IS_AUTHENTICATED_ANONYMOUSLY':
                    $getCollection = 401;
                    $getItem = 401;
                    $putItem = 401;
                    break;
                case 'ROLE_ADMIN':
                    $putItem = 200;
                    $getCollection = 200;
                    $getItem = 200;
                    break;
                case 'ROLE_USER':
                    $putItem = 403;
                    $getCollection = 200;
                    $getItem = 200;
                    break;
            }

            $path = $this->findIriBy(Event::class, ['id' => $event->getId()]);

            // Check user list access
            $client->request('GET', "/events");
            $this->assertResponseStatusCodeSame($getCollection);

            // Check user access
            $client->request('GET', $path);
            $this->assertResponseStatusCodeSame($getItem);

            // Check user write access - change phone number by adding '1' at the end
            $client->request('PUT', $path, [
                'json' => ['venue' => "{$event->getVenue()}1" ]
            ]);
            $this->assertResponseStatusCodeSame($putItem);

        }
    }

    /**
     * Test access for normal user
     */
   public function testEventAccess()
   {
        $client = self::createClient();
        // Check anon access
        $this->eventAccess($client, 'IS_AUTHENTICATED_ANONYMOUSLY');

        // Check user access
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['email' => $this->authedUser['email']]);
        $this->setUserRoles($client, $user, ['ROLE_USER']);

        $client = $this->createAuthedClient();
        $this->eventAccess($client, 'ROLE_USER');

        // Check admin access

        $this->setUserRoles($client, $user, ['ROLE_ADMIN']);
        $this->eventAccess($client, 'ROLE_ADMIN');

   }
}
