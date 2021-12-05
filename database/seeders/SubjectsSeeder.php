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
                'shortname' => 'mat'
            ],
            [
                'name' => 'Kiswahili',
                'shortname' => 'kis'
            ],
            [
                'name' => 'English',
                'shortname' => 'eng'
            ],
            [
                'name' => 'Biology',
                'shortname' => 'bio'
            ],
            [
                'name' => 'Physics',
                'shortname' => 'phy'
            ],
            [
                'name' => 'Chemestry',
                'shortname' => 'che'
            ],
            [
                'name' => 'History',
                'shortname' => 'his'
            ],
            [
                'name' => 'Geography',
                'shortname' => 'geo'
            ],
            [
                'name' => 'CRE',
                'shortname' => 'cre'
            ],
            [
                'name' => 'IRE',
                'shortname' => 'ire'
            ],
            [
                'name' => 'Business Studies',
                'shortname' => 'bst'
            ],
            [
                'name' => 'Agriculture',
                'shortname' => 'agr'
            ],
            [
                'name' => 'Computer Studies',
                'shortname' => 'com'
            ],
            [
                'name' => 'Power Mechanics',
                'shortname' => 'pm'
            ],
            [
                'name' => 'Music',
                'shortname' => 'mus'
            ],
            [
                'name' => 'Art % Design',
                'shortname' => 'ad'
            ],
            [
                'name' => 'French',
                'shortname' => 'fre'
            ],
            [
                'name' => 'Germany',
                'shortname' => 'ger'
            ],
            [
                'name' => 'Drawing & Desing',
                'shortname' => 'dd'
            ],
            [
                'name' => 'Electricity',
                'shortname' => 'ele'
            ],
            [
                'name' => 'Home Science',
                'shortname' => 'hs'
            ],
            [
                'name' => 'Metalwork',
                'shortname' => 'mw'
            ],
            [
                'name' => 'Woodwork',
                'shortname' => 'ww'
            ]

        ];

        array_walk($payload, function($data){
            Subject::create($data);
        });

    }
}
