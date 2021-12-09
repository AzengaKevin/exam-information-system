<?php

namespace App\Actions\Exam;

use App\Models\Exam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CreateScoresTable
{

    public static function invoke(Exam $exam)
    {
        $tableName = Str::slug($exam->shortname);

        Schema::dropIfExists($tableName);

        Schema::create($tableName, function(Blueprint $table) use($exam){
            $table->string('admno')->unique();
            $table->string('level_id');
            $table->string('level_unit_id');
            foreach ($exam->subjects as $subject) {
                $table->jsonb($subject->shortname)->nullable();
            }
            $table->tinyInteger('points', false, true)->nullable();
            $table->char('grade', 2)->nullable();
            $table->double('average')->nullable();
            $table->integer('total', false, true)->nullable();
        });
        
    }
    
}
