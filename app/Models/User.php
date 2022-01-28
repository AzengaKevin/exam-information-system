<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Scopes\NotDiskusUserScope;
use Illuminate\Database\Eloquent\Model;
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

    // protected static function booted()
    // {
    //     static::addGlobalScope(new NotDiskusUserScope);
    // }

    public function authenticatable()
    {
        return $this->morphTo();
    }

    /**
     * User profile photo fil relation
     */
    public function profilePhoto()
    {
        return $this->morphOne(File::class, 'fileable');
    }

    public function image(): ?string
    {
        if(!is_null($this->profilePhoto)){

            return $this->profilePhoto->url();
            
        }

        return null;
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

    /**
     * Get users of a specific type
     */
    public function scopeType($query, string $type)
    {
        $query->where('authenticatable_type', $type);
    }

    /**
     * Check if a the current user is a teacher
     * 
     * @return bool - Whether the user is a teacher or not
     */
    public function isATeacher() : bool
    {
        /** @var Model */
        $authenticatable = $this->authenticatable;

        if(is_null($authenticatable)) return false;

        else return $authenticatable instanceof Teacher;
        
    }

    /**
     * Checks whether the user is an administrator
     * 
     * @return bool
     */
    public function isAdmin() : bool
    {
        /** @var Role */
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);

        return $this->role->is($adminRole);
    }

    /**
     * Checks whether the user is an administrator
     * 
     * @return bool
     */
    public function isSuperAdmin()
    {
        /** @var Role */
        $superAdminRole = Role::firstOrCreate(['name' => Role::SUPER_ROLE]);

        return $this->role->is($superAdminRole);
        
    }

    public function scopeVisible($query)
    {
        $diskusAdminRoleId = Role::firstOrCreate(['name' => Role::SUPER_ROLE])->id;

        $query->whereNull('role_id')->orWhere('role_id', '!=', $diskusAdminRoleId);
    }

}
