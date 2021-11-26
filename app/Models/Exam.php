<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['name','term','shortname','year','start_date','end_date','weight','counts'];

   
    public static function termOptions()
    {
        return ['Term 1','Term 2','Term 3'];
    }

}
