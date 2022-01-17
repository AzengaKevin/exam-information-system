<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class StreamSubjectPerformance extends Pivot
{
    public function levelUnit()
    {
        return $this->belongsTo(LevelUnit::class);
    }
}
