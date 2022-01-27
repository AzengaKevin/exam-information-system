<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'locked'
    ];

    protected $casts = [
        'locked' => 'boolean'
    ];

    /**
     * Name mutation to create the slug
     * @param mixed $value
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;

        $this->attributes['slug'] = Str::slug($value);
        
    }

    /**
     * Mutate the locked attribute to set it to boolean property
     */
    public function setLockedAttribute($value)
    {
        $this->attributes['locked'] = boolval($value);
    }

    /**
     * Role Permission inverse relation
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    /** */
    public function scopeUnLocked($query)
    {
        $query->where('locked', false);
    }
    
}
