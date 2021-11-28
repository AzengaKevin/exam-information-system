<?php

namespace Database\Seeders;

use App\Models\Level;
use Illuminate\Database\Seeder;

class LevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payload = [
            [
                'name' => 'Form One',
                'numeric' => 1
            ],
            [
                'name' => 'Form Two',
                'numeric' => 2
            ],
            [
                'name' => 'Form Three',
                'numeric' => 3
            ],
            [
                'name' => 'Form Four',
                'numeric' => 4
            ]
        ];

        array_walk($payload, function($data){
            Level::create($data);
        });
    }
}
