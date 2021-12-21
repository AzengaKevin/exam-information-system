<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSeeder extends Seeder
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
                'grade' => 'A',
                'points' => 12,
                'english_comment' => 'Excellent work',
                'swahili_comment' => 'Vizuri sana',
            ],
            [
                'grade' => 'A-',
                'points' => 11,
                'english_comment' => 'Very good work',
                'swahili_comment' => 'Vizuri',
            ],
            [
                'grade' => 'B+',
                'points' => 10,
                'english_comment' => 'Good work, aim higher',
                'swahili_comment' => 'Zaidi ya wastani',
            ],
            [
                'grade' => 'B',
                'points' => 9,
                'english_comment' => 'Good work, can do better',
                'swahili_comment' => 'Zaidi ya wastani',
            ],
            [
                'grade' => 'B-',
                'points' => 8,
                'english_comment' => 'Satisfactory, aim higher',
                'swahili_comment' => 'Zaidi ya wastani',
            ],
            [
                'grade' => 'C+',
                'points' => 7,
                'english_comment' => 'Can do better, aim higher',
                'swahili_comment' => 'Wastani',
            ],
            [
                'grade' => 'C',
                'points' => 6,
                'english_comment' => 'Average, aim higher',
                'swahili_comment' => 'Wastani',
            ],
            [
                'grade' => 'C-',
                'points' => 5,
                'english_comment' => 'Below average, can do better',
                'swahili_comment' => 'Wastani',
            ],
            [
                'grade' => 'D+',
                'points' => 4,
                'english_comment' => 'Put more effort',
                'swahili_comment' => 'Wastani',
            ],
            [
                'grade' => 'D',
                'points' => 3,
                'english_comment' => 'Put more effort',
                'swahili_comment' => 'Chini ya wastani',
            ],
            [
                'grade' => 'D-',
                'points' => 2,
                'english_comment' => 'Weak but has potential',
                'swahili_comment' => 'Chini ya wastani',
            ],
            [
                'grade' => 'E',
                'points' => 1,
                'english_comment' => 'Weak but has potential',
                'swahili_comment' => 'Chini ya wastani',
            ],
            [
                'grade' => 'P',
                'points' => 0,
                'english_comment' => 'Pending',
                'swahili_comment' => 'Inasubiri',
            ],
            [
                'grade' => 'X',
                'points' => 0,
                'english_comment' => 'Missing',
                'swahili_comment' => 'Imekosekana',
            ],
            [
                'grade' => 'Y',
                'points' => 0,
                'english_comment' => 'Irregularities',
                'swahili_comment' => 'Udanganyifu',
            ],
            [
                'grade' => 'Z',
                'points' => 0,
                'english_comment' => 'Entry not met',
                'swahili_comment' => 'Ingizo halijafikiwa',
            ]
        ];
        
        array_walk($payload, function($data){
            DB::table('grades')
                ->updateOrInsert([
                    'grade' => $data['grade']
                ],[
                    'points' => $data['points'],
                    'swahili_comment' => $data['swahili_comment'],
                    'english_comment' => $data['english_comment']
                ]);
        });
    }
}
