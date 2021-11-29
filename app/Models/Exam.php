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
        'term',
        'shortname', 
        'slug', 
        'year',
        'start_date',
        'end_date',
        'weight',
        'counts',
        'description'
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

}
