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
        'kcpe_marks',
        'kcpe_grade',
        'admission_level_id',
        'stream_id',
        'level_unit_id',
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

    public function admissionLevel()
    {
        return $this->belongsTo(Level::class, 'admission_level_id');
    }

    public function levelUnit()
    {
        return $this->belongsTo(LevelUnit::class);
    }

    public static function kcpeGradeOptions() : array
    {
        return [
            'A', 'A-', 'B+', 'B', 'B-', 'C+', 'C', 'C-', 'D+', 'D', 'D-', 'E'
        ];
    }

    public function guardians()
    {
        return $this->belongsToMany(Guardian::class,'student_guardians')
            ->withTimestamps();
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class)
            ->withPivot(['mm','tm','mp','tp','mg','sp','op'])
            ->withTimestamps();
    }
}
