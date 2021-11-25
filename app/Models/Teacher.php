<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'employer',
        'tsc_number'
    ];

    public static function employerOptions()
    {
        return [
            'TSC',
            'BOM'
        ];
    }

    public function auth()
    {
        return $this->morphOne(User::class, 'authenticatable');
    }
}
