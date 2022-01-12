<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        $this->attributes['slug'] = Str::slug($value);
        
    }

    /**
     * Role - Permission Relation
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Role - User Relation
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
