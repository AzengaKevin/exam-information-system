<?php

namespace Database\Seeders;

use App\Models\Responsibility;
use Illuminate\Database\Seeder;

class ResponsibilitySeeder extends Seeder
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
                'name' => 'Principal',
                'requirements' => []
            ],
            [
                'name' => 'Deputy',
                'requirements' => []
            ],
            [
                'name' => 'Director of Studies',
                'requirements' => []
            ],
            [
                'name' => 'Level Supervisor',
                'requirements' => ['level']
            ],
            [
                'name' => 'Class Teacher',
                'requirements' => ['class']
            ],
            [
                'name' => 'Subject Teacher',
                'requirements' => ['class', 'subject']]
        ];

        array_walk($payload, function($data){
            Responsibility::create($data); 
        });
    }
}
