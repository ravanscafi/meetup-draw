<?php

namespace App\Meetup;

use App\Meetup\Exceptions\EventNotFoundException;
use App\Meetup\Exceptions\MeetupErrorException;
use DMS\Service\Meetup\MeetupKeyAuthClient;
use Exception;
use Guzzle\Http\Exception\ClientErrorResponseException;
use Guzzle\Http\Exception\ServerErrorResponseException;

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
     * @throws EventNotFoundException
     * @throws MeetupErrorException
     *
     * @return array An Event from Meetup
     */
    public function get($eventId)
    {
        try {
            $response = $this->client->getEvents(['event_id' => $eventId]);
        } catch (ClientErrorResponseException $e) {
            $this->throwNotFound($eventId, $e);
        } catch (ServerErrorResponseException $e) {
            throw new MeetupErrorException('Error on Meetup API!', 0, $e);
        }

        if (false === $response->valid()) {
            $this->throwNotFound($eventId);
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

    /**
     * @param int|string $eventId
     * @param Exception  $previous
     *
     * @throws EventNotFoundException
     */
    protected function throwNotFound($eventId, Exception $previous = null)
    {
        throw new EventNotFoundException("Event '{$eventId}' not found!", 0, $previous);
    }
}
