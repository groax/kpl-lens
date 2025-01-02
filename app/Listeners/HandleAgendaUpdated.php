<?php

namespace App\Listeners;

use App\Models\Agenda;
use App\Services\GoogleCalendarService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

readonly class HandleAgendaUpdated
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private GoogleCalendarService $googleCalendarService
    )
    {
        //
    }

    private function shouldHandle(Agenda $agenda): bool
    {
        return $agenda->in_agenda;
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(object $event): bool
    {
        /** @var Agenda $agenda */
        $agenda = $event->agenda;

        if (!$this->shouldHandle($agenda)) {
            $this->googleCalendarService->delete($agenda);

            // Reset the Google Calendar fields
            $agenda->event_id = NULL;
            $agenda->recurring_event_id = NULL;
            $agenda->ical_uid = NULL;
            $agenda->html_link = NULL;
            $agenda->meet_link = NULL;

            $agenda->save();

            return false;
        }

        $this->googleCalendarService->update($agenda);

        return true;
    }
}
