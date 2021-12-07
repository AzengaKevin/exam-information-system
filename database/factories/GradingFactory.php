<?php

namespace Database\Factories;

use App\Models\Grading;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $payload = array();

        foreach (array_reverse(Grading::gradeOptions()) as $key => $value) {
            array_push($payload, [
                "low" => $this->faker->numberBetween(0, 100),
                "high" => $this->faker->numberBetween(0, 100),
                "grade" => $value,
                "points" => $key + 1
            ]);
        }

        return [
            "name" => $this->faker->sentence(),
            "values" => $payload
        ];
    }
}
