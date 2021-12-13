<?php

namespace Database\Factories;

use App\Models\Responsibility;
use Illuminate\Database\Eloquent\Factories\Factory;

class ResponsibilityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->sentence(),
            'description' => $this->faker->paragraph(),
            'requirements' => $this->faker->randomElements(Responsibility::requirementOptions())
        ];
    }
}
