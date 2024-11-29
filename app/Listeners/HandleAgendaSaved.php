<?php

namespace App\Listeners;

use App\Http\Services\GoogleCalendarService;
use App\Models\Agenda;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

readonly class HandleAgendaSaved
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
        if ($agenda->in_agenda) {
            return true;
        }

        return false;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): bool
    {
        if (! $this->shouldHandle($event->agenda)) {
            return false;
        }

        $this->googleCalendarService->createEvent($event->agenda);

        return true;
    }
}
