<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location',
        'profession'
    ];

    public function auth()
    {
        return $this->morphOne(User::class, 'authenticatable');
    }

    /**
     * Guardian students relation
     */
    public function students()
    {
        return $this->belongsToMany(Student::class,'student_guardians')
            ->withPivot(['primary'])
            ->withTimestamps();
    }
}
