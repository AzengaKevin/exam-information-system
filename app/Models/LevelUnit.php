<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelUnit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'level_id',
        'stream_id',
        'alias',
        'description',
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }
    
    public function exams()
    {
        return $this->belongsToMany(Exam::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average']);
    }

    /**
     * Level Unit Responsibility relation
     * 
     */
    public function responsibilities()
    {
        return $this->belongsToMany(Responsibility::class, 'responsibility_teacher')
            ->using(ResponsibilityTeacher::class)
            ->withTimestamps()
            ->withPivot(['teacher_id', 'subject_id']);
    }
}
