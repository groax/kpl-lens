<?php

namespace App\Traits\Google\Calendar;

use App\Models\Agenda;
use Exception;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

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
    public function listEvents(string $calendarId = 'primary', ?string $timeMin = null, ?string $timeMax = null): array
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
     * Create a new event in Google Calendar.
     *
     * @param Agenda $agenda The agenda model to map to a calendar event.
     * @param string $calendarId The ID of the calendar (use 'primary' for the default calendar).
     * @return Google_Service_Calendar_Event
     * @throws \Google\Service\Exception
     * @throws Exception
     */
    public function createEvent(Agenda $agenda, string $calendarId = 'primary'): Google_Service_Calendar_Event
    {
        // Ensure we have a valid access token
        $accessToken = $this->getValidAccessToken();
        $this->client->setAccessToken($accessToken);

        // Initialize the Google Calendar service
        $service = new Google_Service_Calendar($this->client);

        // Map the Agenda model to a Google Calendar event
        $event = new Google_Service_Calendar_Event([
            'summary' => $agenda->summary,
            'description' => $agenda->description,
            'location' => $agenda->location,
            'start' => new Google_Service_Calendar_EventDateTime([
                'dateTime' => $agenda->start->toIso8601String(),
                'timeZone' => 'Europe/Amsterdam',
            ]),
            'end' => new Google_Service_Calendar_EventDateTime([
                'dateTime' => $agenda->end->toIso8601String(),
                'timeZone' => 'Europe/Amsterdam',
            ]),
        ]);

        // Insert the event into Google Calendar
        return $service->events->insert($calendarId, $event);
    }
}
