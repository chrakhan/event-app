<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'user_id'     => 1,
            'location'    => $this->faker->city(),
            'event_date'  => $this->faker->dateTimeBetween('now', '+1 year'),
            'status'      => 'active',
        ];
    }
}
