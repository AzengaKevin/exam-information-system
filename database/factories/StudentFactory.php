<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'adm_no' => $this->faker->unique()->numberBetween(5000, 10000),
            'name' => $this->faker->name(),
            'dob' => $this->faker->date(),
            'gender' => $this->faker->randomElement(User::genderOptions()),
            'admission_level_id' => Level::factory(),
            'stream_id' => Stream::factory(),
            'description' => $this->faker->paragraph()
        ];
    }
}
