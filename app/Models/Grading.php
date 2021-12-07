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
            'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E'
        ];
    }
}
