<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'sender_id' => User::factory(),
            'recipient_id' => User::factory(),
            'type' => $this->faker->randomElement(Message::typeOptions()),
            'content' => $this->faker->sentence()
        ];
    }
}
