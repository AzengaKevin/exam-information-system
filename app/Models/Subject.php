<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory,SoftDeletes;

    protected $guarded = [];

    protected $casts = ['segments' => 'array'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
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
