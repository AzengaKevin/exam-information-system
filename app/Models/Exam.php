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

   
    public static function termOptions()
    {
        return ['Term 1','Term 2','Term 3'];
    }

    public function setShortnameAttribute($value)
    {
        $this->attributes['shortname'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public static function examStatusOptions() : array
    {
        return [
            'Preparation',
            'In Progress',
            'Marking',
            'Published'
        ];
    }

    public function isInMarking(): bool
    {
        return $this->status == 'Marking';
    }

    public function isPublished(): bool
    {
        return $this->status == 'Published';
    }

    public function setCountsAttribute($value)
    {
        $this->attributes['counts'] = boolval($value);
    }

    public function levels()
    {
        return $this->belongsToMany(Level::class);
    }

    public function levelUnits()
    {
        return $this->belongsToMany(LevelUnit::class)
            ->withTimestamps()
            ->withPivot(['points', 'grade', 'average']);
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }
}
