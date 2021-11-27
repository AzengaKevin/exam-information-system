<?php

namespace Database\Factories;

use App\Models\Level;
use App\Models\Stream;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelUnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'level_id' => Level::factory(),
            'stream_id' => Stream::factory(),
            'alias' => $this->faker->unique()->word(),
            'description' => $this->faker->paragraph()
        ];
    }
}
