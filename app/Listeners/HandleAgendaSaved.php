<?php

namespace App\Listeners;

use App\Models\Agenda;
use App\Services\GoogleCalendarService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

readonly class HandleAgendaSaved
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private GoogleCalendarService $googleCalendarService
    ) {
        //
    }

    private function shouldHandle(Agenda $agenda): bool
    {
        return $agenda->in_agenda
            && ! $agenda->event_id
            && ! $agenda->recurring_event_id
            && ! $agenda->ical_uid
            && ! $agenda->html_link;
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(object $event): bool
    {
        /** @var Agenda $agenda */
        $agenda = $event->agenda;

        if (! $this->shouldHandle($agenda)) {
            return false;
        }

        $googleCalendar = $this->googleCalendarService->create($agenda);

        $agenda->event_id = $googleCalendar->getId();
        $agenda->recurring_event_id = $googleCalendar->getRecurringEventId();
        $agenda->ical_uid = $googleCalendar->getICalUID();
        $agenda->html_link = $googleCalendar->getHtmlLink();
        $agenda->meet_link = $googleCalendar->getHangoutLink();
        $agenda->save();

        return true;
    }
}
