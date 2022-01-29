<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'shortname',
        'slug',
        'optional',
        'segments',
        'department_id',
        'subject_code',
        'description'
    ];

    protected $casts = [
        'segments' => 'array',
        'optional' => 'boolean'
    ];

    public function scopeOptional($query)
    {
        $query->where('optional', true);
    }

    public function scopeCompulsory($query)
    {
        $query->where('optional', false);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    public function setOptionalAttribute($value)
    {
        $this->attributes['optional'] = boolval($value);
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class);
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class)
            ->withTimestamps();
    }
}
