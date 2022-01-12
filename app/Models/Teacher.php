<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer',
        'tsc_number'
    ];

    public static function employerOptions()
    {
        return [
            'TSC',
            'BOM'
        ];
    }

    public function auth()
    {
        return $this->morphOne(User::class, 'authenticatable');
    }

    public function responsibilities()
    {
        return $this->belongsToMany(Responsibility::class)
            ->using(ResponsibilityTeacher::class)
            ->withTimestamps()
            ->withPivot(['level_id', 'level_unit_id', 'subject_id', 'department_id', 'id']);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }
}
