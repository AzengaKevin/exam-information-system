<?php

namespace Database\Factories;

use App\Models\Grading;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'grade' => $this->faker->unique()->randomElement(Grading::gradeOptions()),
            'points' => $this->faker->numberBetween(0, 12),
            'english_comment' => $this->faker->sentence(),
            'swahili_comment' => $this->faker->sentence(),
        ];
    }
}
