<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $payload = [
            'Mathematics Depatment',
            'Sciences Department',
            'Languages Department',
            'Humanities Department',
            'Industrials Department',
        ];

        array_walk($payload, function($data){
            Department::create(['name' => $data]);
        });
    }
}
