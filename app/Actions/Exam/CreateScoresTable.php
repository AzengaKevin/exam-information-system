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
            foreach ($exam->subjects as $subject) {
                $table->jsonb($subject->shortname)->nullable();
            }
            $table->jsonb('average')->nullable();
            $table->integer('total')->nullable();
        });
        
    }
    
}
