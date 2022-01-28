<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'shortname',
        'term',
        'slug', 
        'year',
        'start_date',
        'end_date',
        'weight',
        'counts',
        'description',
        'status',
        'deviation_exam_id'
    ];

    protected $casts = [
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'counts' => 'boolean'
    ];

    /**
     * Define an array of all possible term options
     * @return array
     */
    public static function termOptions() : array
    {
        return ['Term 1','Term 2','Term 3'];
    }

    /**
     * Mutator to set the exam slug name from the shortname
     * 
     * @param mixed $value
     */
    public function setShortnameAttribute($value)
    {
        $this->attributes['shortname'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    /**
     * Define all possible states of an exam
     * 
     * @return
     */
    public static function examStatusOptions() : array
    {
        return [
            'Preparation',
            'In Progress',
            'Marking',
            'Published'
        ];
    }

    /**
     * Check whether an exam is in a marking state
     * 
     * @return bool
     */
    public function isInMarking(): bool
    {
        return $this->status == 'Marking';
    }

    /**
     * Check whether an exam is published
     * 
     * @return bool
     */
    public function isPublished(): bool
    {
        return $this->status == 'Published';
    }

    /**
     * A hook to update the counts attribute when persisting an exam
     * 
     * @param mixed $value
     */
    public function setCountsAttribute($value)
    {
        $this->attributes['counts'] = boolval($value);
    }

    /**
     * Exam status scope method
     */
    public function scopeStatus($query, string $status)
    {
        $query->where('status', $status);
    }

    /**
     * Exam - Deviation Exam Relation
     * 
     */
    public function deviationExam()
    {
        return $this->belongsTo(Exam::class, 'deviation_exam_id');
    }

    /**
     * Defines exam level relation
     */
    public function levels()
    {
        return $this->belongsToMany(Level::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average', 'points_deviation', 'average_deviation']);
    }

    /**
     * Defines exam level-unit relation
     */
    public function levelUnits()
    {
        return $this->belongsToMany(LevelUnit::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average', 'points_deviation', 'average_deviation']);
    }

    /**
     * Defines exam level unit relation
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    /**
     * Defines exam level grade distribution relation
     */
    public function levelGradesDist()
    {
        return $this->belongsToMany(Level::class, 'exam_level_grade_distribution')
            ->withPivot(['grade', 'grade_count'])
            ->withTimestamps();
    }

    /**
     * Define exam level unit grade distribution relation
     */
    public function levelUnitGradesDistribution()
    {
        return $this->belongsToMany(LevelUnit::class, 'exam_level_unit_grade_distribution')
            ->withPivot(['grade', 'grade_count'])
            ->withTimestamps();
        
    }

    /**
     * Defines exam level subject performance relation
     */
    public function levelSubjectPerformance()
    {
        return $this->belongsToMany(Subject::class, 'exam_level_subject_performance', 'exam_id', 'subject_id')
            ->withTimestamps()
            ->withPivot(['points', 'points_deviation', 'grade', 'average', 'average_deviation', 'level_id']);
    }

    /**
     * Defines exam level-unit subject performance relation
     */
    public function levelUnitSubjectPerformance()
    {
        return $this->belongsToMany(Subject::class, 'exam_level_unit_subject_performance')
            ->using(StreamSubjectPerformance::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average', 'level_unit_id', 'points_deviation', 'average_deviation']);
    }

    /**
     * Defines exam student relation
     */
    public function students()
    {
        return $this->belongsToMany(Student::class)
            ->withPivot(['mm','tm','mp','tp','mg','sp','op', 'mmd', 'tmd', 'mpd', 'tpd'])
            ->withTimestamps();
    }

    /**
     * Get all LevelUnits for an exam
     * 
     * @return Collection
     */
    public function getAllLevelUnits()
    {
        return LevelUnit::whereIn('level_id', $this->levels->pluck('id')->all())->get();
    }

    /**
     * Checks whether the current exam matches the previous exam
     * 
     * @param Exam $exam - The other exam to compare tp
     * 
     * @return bool - Whether the exams match or not
     */
    public function matches(Exam $exam) : bool
    {
        $currentExamSubjectsIds = $this->subjects->pluck('id')->all();

        $otherExamSubjectsIds = $exam->subjects->pluck('id')->all();

        $currentExamLevelsIds = $this->levels->pluck('id')->all();

        $otherExamLevelsIds = $exam->levels->pluck('id')->all();

        if(count($currentExamSubjectsIds) != count($otherExamSubjectsIds)) return false;

        if(count($currentExamLevelsIds) != count($otherExamLevelsIds)) return false;
        
        if(!empty(array_diff($currentExamSubjectsIds, $otherExamSubjectsIds))) return false;

        if(!empty(array_diff($currentExamLevelsIds, $otherExamLevelsIds))) return false;

        return true;
    }

    /**
     * Exam - Student relation for top students per subject (level)
     * 
     * @return Relation
     */
    public function levelTopSubjectStudents()
    {
        return $this->belongsToMany(Student::class, 'exam_level_top_students_per_subject')
            ->withTimestamps()
            ->withPivot(['subject_id', 'level_id', 'score', 'grade']);
    }

    /**
     * Exam - Student relation for top students per subject (level-unit)
     * 
     * @return Relation
     */
    public function levelUnitTopSubjectStudents()
    {
        return $this->belongsToMany(Student::class, 'exam_level_unit_top_students_per_subject')
            ->withTimestamps()
            ->withPivot(['subject_id', 'level_unit_id', 'score', 'grade']);
    }

    /**
     * Exam - User action relation
     * 
     * @return Relation
     */
    public function userActivities()
    {
        return $this->belongsToMany(User::class, 'exam_user_activities')
            ->withTimestamps()
            ->withPivot(['subject_id', 'level_id', 'level_unit_id', 'action']);
            // ->using(ExamUserActivity::class);
    }

}
