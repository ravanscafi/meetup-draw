<?php

namespace App\Meetup;

use App\Meetup\Exceptions\EventNotFoundException;
use DMS\Service\Meetup\MeetupKeyAuthClient;

class Event
{
    /**
     * @var MeetupKeyAuthClient
     */
    protected $client;

    /**
     * Event constructor.
     *
     * @param MeetupKeyAuthClient $client
     */
    public function __construct(MeetupKeyAuthClient $client)
    {
        $this->client = $client;
    }

    /**
     * Retrieve a event from Meetup.
     *
     * @param int|string $eventId Desired Event
     *
     * @return array An Event from Meetup
     * @throws EventNotFoundException
     */
    public function get($eventId)
    {
        $response = $this->client->getEvents(['event_id' => $eventId]);

        if (false === $response->valid()) {
            throw new EventNotFoundException("Event '{$eventId}' not found!");
        }

        return $response->current();
    }

    /**
     * Calculate how many open spots are left on the event,
     * so that we can hold a draw on them.
     *
     * @param int|string $eventId Desired Event
     *
     * @return int Number of spots available
     */
    public function getAvailableSpots($eventId)
    {
        $event = $this->get($eventId);

        $spotsLimit = $event['rsvp_limit'] ?? 0;
        $spotsTaken = $event['yes_rsvp_count'] ?? 0;

        return $spotsLimit - $spotsTaken;
    }

    /**
     * Retrieve participants for the draw from event.
     * A participant is someone that RSVP'ed as 'waitlist'.
     *
     * @param int|string $eventId Desired event
     *
     * @return array Eligible Participants for the Draw
     */
    public function getParticipants($eventId)
    {
        $response = $this->client->getRsvps(['event_id' => $eventId]);

        $candidates = iterator_to_array($response);

        $participants = array_filter($candidates, function ($candidate) {
            return 'waitlist' === ($candidate['response'] ?? '');
        });

        return array_values($participants);
    }
}
