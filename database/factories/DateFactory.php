<?php

namespace Database\Factories;

use App\Models\Agenda;
use Illuminate\Database\Eloquent\Factories\Factory;

class DateFactory extends Factory
{
    protected $model = Agenda::class;

    public function definition(): array
    {
        return [
            'getDurationStartEnd' => $this->faker->word(),
        ];
    }
}
