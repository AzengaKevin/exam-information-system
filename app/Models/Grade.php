<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade',
        'points',
        'swahili_comment',
        'english_comment',
        'ct_comment',
        'p_comment'
    ];
}
