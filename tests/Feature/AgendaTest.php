<?php

namespace Tests\Feature;

use App\Models\Agenda;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    // This method name should start with 'test'
    public function testAgendaEndIsAfterThenStart(): void
    {
        $agenda = Agenda::factory()->create();

        $this->assertGreaterThan($agenda->start, $agenda->end);
    }

    // This method name should also start with 'test'
    public function testAgendaEndIsBeforeThenStart(): void
    {
        $agenda = Agenda::factory([
            'start' => now()->addHour(),
            'end' => now(),
        ])->create();

        $this->assertLessThan($agenda->start, $agenda->end);
    }
}

