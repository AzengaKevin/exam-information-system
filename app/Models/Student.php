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
        'upi',
        'description'
    ];

    protected $casts = [
        'dob' => 'date'
    ];

    public function setAdmissionLevelIdAttribute($value)
    {
        $this->attributes['admission_level_id'] = $value;
        $this->attributes['level_id'] = $value;
    }

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
