<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

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

    /**
     * Teacher - User Relation
     */
    public function auth()
    {
        return $this->morphOne(User::class, 'authenticatable');
    }

    /**
     * Teacher - Responsibilities Relation
     */
    public function responsibilities()
    {
        return $this->belongsToMany(Responsibility::class)
            ->using(ResponsibilityTeacher::class)
            ->withTimestamps()
            ->withPivot(['level_id', 'level_unit_id', 'subject_id', 'department_id', 'id']);
    }

    /**
     * Teacher Subjects Relation
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class)->withTimestamps();
    }

    /**
     * Check if a teacher is a director of studies
     * 
     * @return bool
     */
    public function isDos() : bool
    {
        /** @var Responsibility */
        $dosRes = Responsibility::firstOrCreate(['name' => 'Director of Studies']);

        return $this->responsibilities->contains($dosRes);
    }

    /**
     * Check if a teacher is the exam manager
     * 
     * @return bool
     */
    public function isExamManager() : bool
    {
        return false;
    }

    /**
     * Check if a teacher is the school manager
     */
    public function isSchoolManager() : bool
    {
        return false;
    }
}
