<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'stream_id',
        'alias',
        'description',
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }
}
