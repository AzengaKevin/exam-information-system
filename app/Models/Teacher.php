<?php

namespace App\Models;

use App\Settings\GeneralSettings;
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
        return ['TSC','BOM'];
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
        /** @var GeneralSettings */
        $generalSettings = app(GeneralSettings::class);

        $examManagerResp = Responsibility::find($generalSettings->exam_manager_responsibility_id);

        if (is_null($examManagerResp)) $examManagerResp = Responsibility::firstOrCreate(['name' => "Exam Manager"]);

        return $this->responsibilities->contains($examManagerResp);
    }

    /**
     * Check if a teacher is the school manager
     */
    public function isSchoolManager() : bool
    {
        /** @var GeneralSettings */
        $generalSettings = app(GeneralSettings::class);

        $schoolManagerResp = Responsibility::find($generalSettings->exam_manager_responsibility_id);

        if (is_null($schoolManagerResp)) $schoolManagerResp = Responsibility::firstOrCreate(['name' => "Principal"]);

        return $this->responsibilities->contains($schoolManagerResp);
    }
}
