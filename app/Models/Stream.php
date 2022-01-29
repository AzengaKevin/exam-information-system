<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stream extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'alias',
        'description'
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        $this->attributes['slug'] = Str::slug($value);
        
    }

    /** 
     * Stream Student Relation
     * 
     * @return Relation
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Stream Subject Relation
     * 
     * @return Relation
     */
    public function optionalSubjects()
    {
        return $this->belongsToMany(Subject::class)
            ->withTimestamps();
    }

}
