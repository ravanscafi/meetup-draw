<?php

namespace App\Meetup;

use App\Meetup\Exceptions\EventNotFoundException;
use App\Meetup\Exceptions\MeetupErrorException;
use ArrayIterator;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;
use PHPUnit_Framework_TestCase;

class EventTest extends PHPUnit_Framework_TestCase
{
    public function testShouldGetEvent()
    {
        // Set
        $client = $this->getMock(MeetupKeyAuthClient::class, ['getEvents']);
        $event = new Event($client);
        $eventId = '123';

        $meetupEvent = [
            'awesome' => 'event',
        ];

        // Expectations
        $client->method('getEvents')
            ->with(['event_id' => $eventId])
            ->willReturn(new ArrayIterator([$meetupEvent]));

        // Actions
        $result = $event->get($eventId);

        // Assertions
        $this->assertEquals($meetupEvent, $result);
    }

    public function testShouldNotGetEventWhenResponseIsInvalid()
    {
        // Set
        $client = $this->getMock(MeetupKeyAuthClient::class, ['getEvents']);
        $event = new Event($client);
        $eventId = '123';

        // Expectations
        $client->method('getEvents')
            ->with(['event_id' => $eventId])
            ->willReturn(new ArrayIterator());

        $this->setExpectedException(
            EventNotFoundException::class,
            "Event '123' not found!"
        );

        // Actions
        $event->get($eventId);
    }

    public function testShouldNotGetEventWhenAClientExceptionIsThrown()
    {
        // Set
        $client = $this->getMock(MeetupKeyAuthClient::class, ['getEvents']);
        $event = new Event($client);
        $eventId = '123';
        $exception = new ClientErrorResponseException();

        // Expectations
        $client->method('getEvents')
            ->with(['event_id' => $eventId])
            ->willThrowException($exception);

        $this->setExpectedException(
            EventNotFoundException::class,
            "Event '123' not found!"
        );

        // Actions
        $event->get($eventId);
    }

    public function testShouldNotGetEventWhenAServerExceptionIsThrown()
    {
        // Set
        $client = $this->getMock(MeetupKeyAuthClient::class, ['getEvents']);
        $event = new Event($client);
        $eventId = '123';
        $exception = new ServerErrorResponseException();

        // Expectations
        $client->method('getEvents')
            ->with(['event_id' => $eventId])
            ->willThrowException($exception);

        $this->setExpectedException(
            MeetupErrorException::class,
            "Error on Meetup API!"
        );

        // Actions
        $event->get($eventId);
    }

    public function testShouldGetAvailableSpots()
    {
        // Set
        $client = $this->getMock(MeetupKeyAuthClient::class, ['getEvents']);
        $event = new Event($client);
        $eventId = '123';

        $meetupEvent = [
            'rsvp_limit'     => 70,
            'yes_rsvp_count' => 12,
        ];

        // Expectations
        $client->method('getEvents')
            ->with(['event_id' => $eventId])
            ->willReturn(new ArrayIterator([$meetupEvent]));

        // Actions
        $result = $event->getAvailableSpots($eventId);

        // Assertions
        $this->assertEquals(58, $result);
    }

    public function testShouldGetParticipants()
    {
        // Set
        $client = $this->getMock(MeetupKeyAuthClient::class, ['getRsvps']);
        $event = new Event($client);
        $eventId = '123';

        $participants = new ArrayIterator([
            ['participant 1' => 'john doe', 'response' => 'yes'],
            ['participant 2' => 'jane doe', 'response' => 'waitlist'],
            ['participant 3' => 'jack doe', 'response' => 'waitlist'],
            ['participant 4' => 'james doe', 'response' => 'no'],
        ]);

        $expected = [$participants[1], $participants[2]];

        // Expectations
        $client->method('getRsvps')
            ->with(['event_id' => $eventId])
            ->willReturn($participants);

        // Actions
        $result = $event->getParticipants($eventId);

        // Assertions
        $this->assertEquals($expected, $result);
    }
}
