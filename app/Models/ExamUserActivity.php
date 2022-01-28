<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ExamUserActivity extends Pivot
{
    use HasFactory;

    /**
     * ExamUserActivity Level Relation
     * 
     * @return Relation
     */
    public function level()
    {
        return $this->belongsTo(Level::class)->withDefault(['name' => '-']);
    }

    /**
     * ExamUserActivity LevelUnit Relation
     * 
     * @return Relation
     */
    public function levelUnit()
    {
        return $this->belongsTo(LevelUnit::class)->withDefault(['alias' => '-']);
    }

    /**
     * ExamUserActivity Subject Relation
     * @return Relation
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class)->withDefault(['name' => '-']);
    }
}
