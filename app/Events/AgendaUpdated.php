<?php

namespace App\Events;

use App\Models\Agenda;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgendaUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Agenda $agenda;

    /**
     * Create a new event instance.
     */
    public function __construct(Agenda $agenda)
    {
        $this->agenda = $agenda;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('agenda-updated'),
        ];
    }
}
