<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $colors = ['blue', 'green', 'purple', 'orange', 'pink', 'red'];
        $hour   = $this->faker->numberBetween(8, 18);

        return [
            'title'       => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(),
            'user_id'     => 1,
            'location'    => $this->faker->city(),
            'event_date'  => $this->faker->dateTimeBetween('now', '+1 year'),
            'status'      => $this->faker->randomElement(['active', 'cancelled', 'completed']),
            'color'       => $this->faker->randomElement($colors),
            'start_time'  => sprintf('%02d:00:00', $hour),
        ];
    }
}
