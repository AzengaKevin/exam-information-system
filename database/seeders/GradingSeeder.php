<?php

namespace Database\Seeders;

use App\Models\Grading;
use Illuminate\Database\Seeder;

class GradingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Grading::create([
            'name' => 'Default Grading System',
            'values' => [
                [
                    'grade' => 'A',
                    'points' => '12',
                    'min' => '80',
                    'max' => '100',
                ],
                [
                    'grade' => 'A-',
                    'points' => '11',
                    'min' => '75',
                    'max' => '79',
                ],
                [
                    'grade' => 'B+',
                    'points' => '10',
                    'min' => '70',
                    'max' => '74',
                ],
                [
                    'grade' => 'B',
                    'points' => '9',
                    'min' => '65',
                    'max' => '69',
                ],
                [
                    'grade' => 'B-',
                    'points' => '8',
                    'min' => '60',
                    'max' => '64',
                ],
                [
                    'grade' => 'C+',
                    'points' => '7',
                    'min' => '55',
                    'max' => '59',
                ],
                [
                    'grade' => 'C',
                    'points' => '6',
                    'min' => '50',
                    'max' => '54',
                ],
                [
                    'grade' => 'C-',
                    'points' => '5',
                    'min' => '45',
                    'max' => '49',
                ],
                [
                    'grade' => 'D+',
                    'points' => '4',
                    'min' => '40',
                    'max' => '44',
                ],
                [
                    'grade' => 'D',
                    'points' => '3',
                    'min' => '35',
                    'max' => '39',
                ],
                [
                    'grade' => 'D-',
                    'points' => '2',
                    'min' => '30',
                    'max' => '34',
                ],
                [
                    'grade' => 'E',
                    'points' => '1',
                    'min' => '0',
                    'max' => '29',
                ],
                [
                    'grade' => 'X',
                    'points' => '0',
                    'min' => null,
                    'max' => null,
                ],
                [
                    'grade' => 'Y',
                    'points' => '0',
                    'min' => null,
                    'max' => null,
                ],
                [
                    'grade' => 'Z',
                    'points' => '0',
                    'min' => null,
                    'max' => null,
                ]
            ]
        ]);
    }
}
