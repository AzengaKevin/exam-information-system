<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class HostelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'Menengai',
                'Abadares',
                'Ngong',
                'Kilimanajro',
                'Kenya',
                'Ruwenzori',
                'Elgon',
                'Longonot',
                'Meru',
                'Mfumbiro',
                'Suswa'
            ]),
            'description' => $this->faker->paragraph()
        ];
    }
}
