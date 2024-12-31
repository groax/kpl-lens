<?php

namespace App\Listeners;

use App\Models\Agenda;
use App\Services\GoogleCalendarService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

readonly class HandleAgendaDeleted
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private GoogleCalendarService $googleCalendarService
    ) {
        //
    }

    /**
     * @throws \Google\Service\Exception
     */
    private function shouldHandle(Agenda $agenda): bool
    {;
        return $this->googleCalendarService->get($agenda->event_id)->valid();
    }

    /**
     * Handle the event.
     * @throws Exception
     */
    public function handle(object $event): bool
    {
        /** @var Agenda $agenda */
        $agenda = $event->agenda;

//        if (! $this->shouldHandle($agenda)) {
//            return false;
//        }

        $this->googleCalendarService->delete($agenda);

        return true;
    }
}
