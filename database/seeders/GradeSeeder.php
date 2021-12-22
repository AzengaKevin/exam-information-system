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
                'ct_comment' => 'Excellent work, keep it up',
                'p_comment' => 'Congradulations, excellent mean grade'
            ],
            [
                'grade' => 'A-',
                'points' => 11,
                'english_comment' => 'Very good work',
                'swahili_comment' => 'Vizuri',
                'ct_comment' => 'Excellent work, keep it up',
                'p_comment' => 'Very good performance but work on your weak areas'
            ],
            [
                'grade' => 'B+',
                'points' => 10,
                'english_comment' => 'Good work, aim higher',
                'swahili_comment' => 'Zaidi ya wastani',
                'ct_comment' => 'Keep working hard for better grades',
                'p_comment' => 'The hard work is paying off, aim higher'
            ],
            [
                'grade' => 'B',
                'points' => 9,
                'english_comment' => 'Good work, can do better',
                'swahili_comment' => 'Zaidi ya wastani',
                'ct_comment' => 'Good work, aim higher',
                'p_comment' => 'You have room for improvement, work harder'
            ],
            [
                'grade' => 'B-',
                'points' => 8,
                'english_comment' => 'Satisfactory, aim higher',
                'swahili_comment' => 'Zaidi ya wastani',
                'ct_comment' => 'Aim higher, you are cable of doing better',
                'p_comment' => 'Good work but aim higher you have potential of doing better'
            ],
            [
                'grade' => 'C+',
                'points' => 7,
                'english_comment' => 'Can do better, aim higher',
                'swahili_comment' => 'Wastani',
                'ct_comment' => 'Average performance, you can do much better than this',
                'p_comment' => 'You have potential, you can do better'
            ],
            [
                'grade' => 'C',
                'points' => 6,
                'english_comment' => 'Average, aim higher',
                'swahili_comment' => 'Wastani',
                'ct_comment' => 'Average performance, aim higher',
                'p_comment' => 'You are capable of doing better, aim higher'
            ],
            [
                'grade' => 'C-',
                'points' => 5,
                'english_comment' => 'Below average, can do better',
                'swahili_comment' => 'Wastani',
                'ct_comment' => 'You are capable of more, put more effort',
                'p_comment' => 'You can do better than this, you have the potential'
            ],
            [
                'grade' => 'D+',
                'points' => 4,
                'english_comment' => 'Put more effort',
                'swahili_comment' => 'Wastani',
                'ct_comment' => 'You have room for improvement, focus on your weak areas',
                'p_comment' => 'You have potential, pay more attention to you studies'
            ],
            [
                'grade' => 'D',
                'points' => 3,
                'english_comment' => 'Put more effort',
                'swahili_comment' => 'Chini ya wastani',
                'ct_comment' => 'You have room for improvement, focus on your weak areas',
                'p_comment' => 'You have potential, pay more attention to you studies'
            ],
            [
                'grade' => 'D-',
                'points' => 2,
                'english_comment' => 'Weak but has potential',
                'swahili_comment' => 'Chini ya wastani',
                'ct_comment' => 'You are weak in you academic, pull up',
                'p_comment' => 'You are sailing in a dangerous zone, watch out'
            ],
            [
                'grade' => 'E',
                'points' => 1,
                'english_comment' => 'Weak but has potential',
                'swahili_comment' => 'Chini ya wastani',
                'ct_comment' => 'You are weak in you academic, pull up',
                'p_comment' => 'You are sailing in a dangerous zone, watch out'
            ],
            [
                'grade' => 'P',
                'points' => 0,
                'english_comment' => 'Pending',
                'swahili_comment' => 'Inasubiri',
                'ct_comment' => 'Your results are pending',
                'p_comment' => 'Your results are pending'
            ],
            [
                'grade' => 'X',
                'points' => 0,
                'english_comment' => 'Missing',
                'swahili_comment' => 'Imekosekana',
                'ct_comment' => 'Avoid missing exams',
                'p_comment' => 'Ensure you sit for all exams next time'
            ],
            [
                'grade' => 'Y',
                'points' => 0,
                'english_comment' => 'Irregularities',
                'swahili_comment' => 'Udanganyifu',
                'ct_comment' => 'Stop cheating in exams',
                'p_comment' => 'Watch out, this will not be condoned'
            ],
            [
                'grade' => 'Z',
                'points' => 0,
                'english_comment' => 'Entry not met',
                'swahili_comment' => 'Ingizo halijafikiwa',
                'ct_comment' => 'Avoid missing exams',
                'p_comment' => 'Ensure you sit for all exams next time'
            ]
        ];
        
        array_walk($payload, function($data){
            DB::table('grades')
                ->updateOrInsert([
                    'grade' => $data['grade']
                ],[
                    'points' => $data['points'],
                    'swahili_comment' => $data['swahili_comment'],
                    'english_comment' => $data['english_comment'],
                    'ct_comment' => $data['ct_comment'],
                    'p_comment' => $data['p_comment'],
                ]);
        });
    }
}
