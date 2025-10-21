<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */
class TicketFactory extends Factory
{

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $states = ['closed', 'open', 'awaiting'];
        $priorities = ['low','medium','high','critical'];
        return [
            'issue' => fake()->name(),
            'priority' => $priorities[rand(0,3)],
            'user_id' => 1,
            'state' => $states[rand(0,2)]
        ];
    }
}
