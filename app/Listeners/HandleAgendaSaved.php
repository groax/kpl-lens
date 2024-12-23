<?php

namespace App\Listeners;

use App\Models\Agenda;
use App\Services\GoogleCalendarService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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

        $googleCalendar = $this->googleCalendarService->createEvent($agenda);

        // $table->id();
        // $table->string('event_id')->unique()->nullable(); // Store the `id`
        // $table->string('recurring_event_id')->nullable(); // Store `recurringEventId` if applicable
        // $table->string('ical_uid')->nullable(); // Store `iCalUID`
        // $table->string('html_link')->nullable(); // Store `htmlLink`

        // $table->string('summary')->nullable(); // Event title
        // $table->boolean('in_agenda')->default(false);
        // $table->boolean('meet_link')->default(false);
        // $table->text('description')->nullable();
        // $table->string('location')->nullable();
        // $table->string('type');
        // $table->dateTime('start')->nullable();
        // $table->dateTime('end')->nullable();

        $agenda->event_id = $googleCalendar->getId();
        $agenda->recurring_event_id = $googleCalendar->getRecurringEventId();
        $agenda->ical_uid = $googleCalendar->getICalUID();
        $agenda->html_link = $googleCalendar->getHtmlLink();
        $agenda->meet_link = $googleCalendar->getHangoutLink();
        $agenda->save();

        $agenda->save();





        return true;
    }
}
