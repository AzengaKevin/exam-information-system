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
        'status'
    ];

    protected $casts = [
        // 'start_date' => 'date',
        // 'end_date' => 'date',
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
     * Defines exam level relation
     */
    public function levels()
    {
        return $this->belongsToMany(Level::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average']);
    }

    public function levelUnits()
    {
        return $this->belongsToMany(LevelUnit::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average']);
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
            ->withPivot(['points', 'grade', 'average', 'level_id']);
    }

    /**
     * Defines exam level-unit subject performance relation
     */
    public function levelUnitSubjectPerformance()
    {
        return $this->belongsToMany(Subject::class, 'exam_level_unit_subject_performance')
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average', 'level_unit_id']);
        
    }

    /**
     * Defines exam student relation
     */
    public function students()
    {
        return $this->belongsToMany(Student::class)
            ->withPivot(['mm','tm','mp','tp','mg','sp','op'])
            ->withTimestamps();
    }

}
