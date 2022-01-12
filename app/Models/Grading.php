<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grading extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'values'
    ];

    protected $casts = [
        'values' => 'array'
    ];

    /**
     * Defining of grades options available
     * 
     * @return array
     */
    public static function gradeOptions(): array
    {
        return [
            'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E', 'P', 'X', 'Y', 'Z' 
        ];
    }
    
}
