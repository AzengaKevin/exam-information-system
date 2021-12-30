<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'active',
        'role_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'boolean'
    ];

    public function authenticatable()
    {
        return $this->morphTo();
    }

    /**
     * Makes sure the phone number starts with 254
     */
    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = Str::start($value, "254");
    }

    public static function genderOptions() : array
    {
        return ['Male', 'Female', 'Other'];
    }

    public function role()
    {
        return $this->belongsTo(Role::class)
            ->withDefault(['name' => 'None']);
    }

    /**
     * User messages relation
     */
    public function messages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }
}
