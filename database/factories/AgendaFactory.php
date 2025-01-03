<?php

namespace Database\Factories;

use App\Enums\DateType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class AgendaFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::now();
        return [
            'event_id' => Str::random(),
            'recurring_event_id' => null,
            'ical_uid' => null,
            'html_link' => null,
            'summary' => $this->faker->text(maxNbChars: 20),
            'in_agenda' => false,
            'meet_link' => false,
            'description' => $this->faker->text(maxNbChars: 100),
            'location' => '',
            'type' => DateType::OTHER,
            'start' => $date,
            'end' => $date->copy()->addHour(),
        ];
    }
}
