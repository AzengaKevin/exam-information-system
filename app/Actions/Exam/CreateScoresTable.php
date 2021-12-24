<?php

namespace App\Actions\Exam;

use App\Models\Exam;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;

class CreateScoresTable
{

    /**
     * Create exam scores table on the fly
     * 
     * @param Exam $exam the one to create the table for
     * 
     * @param bool $refresh fresh the table if it already exixsts
     */
    public static function invoke(Exam $exam, bool $refresh = false)
    {
        $tableName = Str::slug($exam->shortname);

        if(Schema::hasTable($tableName)){

            if(!$refresh){
                return;
            }

        }

        Schema::dropIfExists($tableName);

        Schema::create($tableName, function(Blueprint $table) use($exam){
            $table->string('admno')->unique();
            $table->string('level_id');
            $table->string('level_unit_id')->nullable();
            foreach ($exam->subjects as $subject) {
                $table->jsonb($subject->shortname)->nullable();
            }
            $table->tinyInteger('mp', false, true)->nullable();
            $table->tinyInteger('tp', false, true)->nullable();
            $table->char('mg', 2)->nullable();
            $table->double('mm')->nullable();
            $table->integer('tm', false, true)->nullable();
            $table->integer('sp')->nullable();
            $table->integer('op')->nullable();
        });
        
    }
    
}
