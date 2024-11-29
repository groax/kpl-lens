<?php

namespace App\Http\Services;

use App\Models\Agenda;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;

class GoogleCalendarService
{
    private Event $event;

    public function __construct()
    {
        $this->event = new event;
    }

    public function createEvent(Agenda $agenda): Event
    {
        $event = $this->event;

        $event->name = $agenda->title;
        $event->description = $agenda->description;
        $event->startDateTime = $agenda->start;
        $event->endDateTime = $agenda->end;
//        $event->addAttendee([
//            'email' => 'john@example.com',
//            'name' => 'John Doe',
//            'comment' => 'Lorum ipsum',
//            'responseStatus' => 'needsAction',
//        ]);
        $event->addAttendee(['email' => 'rick.holtman@groax.com']);
        if ($agenda->meet_link) {
            $event->addMeetLink(); // optionally add a google meet link to the event
        }

       return $event->save();
    }
}
