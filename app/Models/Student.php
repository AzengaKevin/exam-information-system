<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'adm_no',
        'name',
        'dob',
        'gender',
        'level_id',
        'admission_level_id',
        'stream_id',
        'upi'
    ];
}
