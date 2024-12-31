<?php

namespace App\Traits\Google\Calendar;

use App\Models\Agenda;
use Carbon\Carbon;
use Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Illuminate\Support\Arr;

trait EventGoogleCalendarTrait
{
    /**
     * Retrieve all events from a calendar.
     *
     * @param string $calendarId The ID of the calendar (use 'primary' for the default calendar).
     * @param string|null $timeMin ISO8601 date-time string to filter events starting from this date.
     * @param string|null $timeMax ISO8601 date-time string to filter events up to this date.
     * @return array
     * @throws Exception
     */
    public function all(string $calendarId = 'primary', ?string $timeMin = null, ?string $timeMax = null): array
    {
        // Ensure we have a valid access token
        // Set the access token in the client
        $this->client->setAccessToken($this->getValidAccessToken());

        // Initialize the Google Calendar service
        $service = new Google_Service_Calendar($this->client);

        // Set optional parameters
        $optParams = [
            'singleEvents' => true,  // Expands recurring events into individual instances
            'orderBy' => 'startTime', // Sort events by start time
        ];

        if ($timeMin) $optParams['timeMin'] = $timeMin;
        if ($timeMax) $optParams['timeMax'] = $timeMax;

        // Fetch events
        $events = $service->events->listEvents($calendarId, $optParams);

        // Return the events as an array
        return $events->getItems();
    }

    /**
     * Retrieve a specific event from Google Calendar.
     *
     * @param string $eventId The ID of the event to retrieve.
     * @param string $calendarId The ID of the calendar (use 'primary' for the default calendar).
     * @return Google_Service_Calendar_Event
     * @throws \Google\Service\Exception
     * @throws Exception
     */
    public function get(string $eventId, string $calendarId = 'primary'): Google_Service_Calendar_Event
    {
        // Ensure we have a valid access token
        $accessToken = $this->getValidAccessToken();
        $this->client->setAccessToken($accessToken);

        // Initialize the Google Calendar service
        $service = new Google_Service_Calendar($this->client);

        // Fetch the event
        return $service->events->get($calendarId, $eventId);
    }

    /**
     * Create a new event in Google Calendar.
     *
     * @param Agenda $agenda The agenda model to map to a calendar event.
     * @param string $calendarId The ID of the calendar (use 'primary' for the default calendar).
     * @return Google_Service_Calendar_Event
     * @throws \Google\Service\Exception
     * @throws Exception
     */
    public function create(Agenda $agenda, string $calendarId = 'primary'): Google_Service_Calendar_Event
    {
        // Ensure we have a valid access token
        $accessToken = $this->getValidAccessToken();
        $this->client->setAccessToken($accessToken);

        // Initialize the Google Calendar service
        $service = new Google_Service_Calendar($this->client);

        // Create a new event object
        $event = new Google_Service_Calendar_Event();

        // Map the fields from the Agenda model to the event
        $event->setSummary($agenda->summary);
        $event->setDescription($agenda->description);
        $event->setLocation($agenda->location);

        // Set start and end times
        $event->setStart(new Google_Service_Calendar_EventDateTime([
            'dateTime' => $agenda->start->toIso8601String(),
            'timeZone' => 'Europe/Amsterdam',
        ]));
        $event->setEnd(new Google_Service_Calendar_EventDateTime([
            'dateTime' => $agenda->end->toIso8601String(),
            'timeZone' => 'Europe/Amsterdam',
        ]));

        // Insert the event into Google Calendar
        return $service->events->insert($calendarId, $event);
    }

    /**
     * Update an existing event in Google Calendar.
     *
     * @param Agenda $agenda
     * @param string $calendarId The ID of the calendar (use 'primary' for the default calendar).
     * @return Google_Service_Calendar_Event
     * @throws \Google\Service\Exception
     * @throws Exception
     */
    public function update(Agenda $agenda, string $calendarId = 'primary'): Google_Service_Calendar_Event
    {
        // Ensure we have a valid access token
        $accessToken = $this->getValidAccessToken();
        $this->client->setAccessToken($accessToken);

        // Initialize the Google Calendar service
        $service = new Google_Service_Calendar($this->client);

        // Retrieve the existing event
        $event = $service->events->get($calendarId, $agenda->event_id);

        // If the event does not exist, create a new one
        // This can happen if the event was deleted in Google Calendar
        if (! $event) {
            return $this->create($agenda, $calendarId);
        }

        // Map the updated fields from the Agenda model to the event
        $event->setSummary($agenda->summary);
        $event->setDescription($agenda->description);
        $event->setLocation($agenda->location);

        // Update start and end times
        $event->setStart(new Google_Service_Calendar_EventDateTime([
            'dateTime' => $agenda->start->toIso8601String(),
            'timeZone' => 'Europe/Amsterdam',
        ]));
        $event->setEnd(new Google_Service_Calendar_EventDateTime([
            'dateTime' => $agenda->end->toIso8601String(),
            'timeZone' => 'Europe/Amsterdam',
        ]));

        // Update the event in Google Calendar
        return $service->events->update($calendarId, $agenda->event_id, $event);
    }


    /**
     * Delete an event from Google Calendar.
     *
     * @param Agenda $agenda
     * @param string $calendarId The ID of the calendar (use 'primary' for the default calendar).
     * @return bool
     * @throws \Google\Service\Exception
     * @throws Exception
     */
    public function delete(Agenda $agenda, string $calendarId = 'primary'): bool
    {
        // Ensure we have a valid access token
        $accessToken = $this->getValidAccessToken();
        $this->client->setAccessToken($accessToken);

        // Initialize the Google Calendar service
        $service = new Google_Service_Calendar($this->client);

        // Delete the event
        $service->events->delete($calendarId, $agenda->event_id);

        return true; // If no exception is thrown, the event was successfully deleted
    }
}
