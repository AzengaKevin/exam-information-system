<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
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
            'shortname' => $this->faker->unique()->word(),
            'description' => $this->faker->paragraph(),
            'department_id' => Department::factory()
        ];
    }
}
