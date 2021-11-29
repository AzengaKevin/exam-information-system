<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'=>$this->faker->sentence(),
            'term' => $this->faker->randomElement(Exam::termOptions()),
            'shortname'=>$this->faker->unique()->word(),
            'year'=>$this->faker->year(),
            'start_date'=>$this->faker->date(),
            'end_date'=>$this->faker->date(),
            'weight'=>$this->faker->numberBetween(10,100),
            'counts'=>$this->faker->boolean(),
            'description' => $this->faker->paragraph()
        ];
    }
}
