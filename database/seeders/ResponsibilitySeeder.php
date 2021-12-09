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
            'Principal',
            'Deputy Principal',
            'Senior Teacher',
            'Director of Studies',
            'Games Coordinator',
            'Guiding and Counselling Teacher',
            'Head of Department',
            'Level Supervisor',
            'Class Teacher',
            'Subject Teacher'
        ];

        array_walk($payload, function($data){
            Responsibility::create(['name' => $data]); 
        });
    }
}
