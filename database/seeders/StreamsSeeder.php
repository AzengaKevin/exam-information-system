<?php

namespace Database\Seeders;

use App\Models\Stream;
use Illuminate\Database\Seeder;

class StreamsSeeder extends Seeder
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
                'name' => 'Blue',
                'alias' => 'B'
            ],
            [
                'name' => 'Green',
                'alias' => 'G'
            ],
            [
                'name' => 'Red',
                'alias' => 'R'
            ],
            [
                'name' => 'White',
                'alias' => 'W'
            ],
            [
                'name' => 'Yellow',
                'alias' => 'Y'
            ],
        ];

        array_walk($payload, function($data){
            Stream::create($data);
        });
    }
}
