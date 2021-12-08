<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grading extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'values'
    ];

    protected $casts = [
        'values' => 'array'
    ];

    public static function gradeOptions(): array
    {
        return [
            'Y', 'X', 'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E'
        ];
    }

    public static function pointsGradeMap() : array
    {
        return [
            12 => 'A',
            11 => 'A-',
            10 => 'B+',
            9 => 'B',
            8 => 'B-',
            7 => 'C+',
            6 => 'C',
            5 => 'C-',
            4 => 'D+',
            3 => 'D',
            2 => 'D-',
            1 => 'E',
        ];
    }
}
