<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectsSeeder extends Seeder
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
                'name' => 'Mathematics',
                'shortname' => 'Math'
            ],
            [
                'name' => 'Kiswahili',
                'shortname' => 'Kisw'
            ],
            [
                'name' => 'English',
                'shortname' => 'Eng'
            ]

        ];

        array_walk($payload, function($data){
            Subject::create($data);
        });


    }
}
