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
            $table->foreignId('student_id')->unique()->constrained()->onDelete('cascade');
            $table->string('level_id');
            $table->string('level_unit_id')->nullable();
            foreach ($exam->subjects as $subject) $table->jsonb($subject->shortname)->nullable();
            $table->tinyInteger('mp', false, true)->nullable();
            $table->tinyInteger('tp', false, true)->nullable();
            $table->char('mg', 2)->nullable();
            $table->double('mm')->nullable();
            $table->integer('tm', false, true)->nullable();
            $table->integer('sp')->nullable();
            $table->integer('op')->nullable();
            $table->double('mmd')->nullable();
            $table->double('tmd')->nullable();
            $table->integer('tpd')->nullable();
            $table->integer('mpd')->nullable();
        });
        
    }
    
    /**
     * Update a table and add columns that don't exist yet
     * 
     * @param Exam $exam
     */
    public static function updateScoresTable(Exam $exam)
    {
        $tableName = Str::slug($exam->shortname);

        Schema::table($tableName, function(Blueprint $table) use($exam, $tableName){
            if(!Schema::hasColumn($tableName, 'student_id')) $table->foreignId('student_id')->unique()->constrained()->onDelete('cascade');
            if(!Schema::hasColumn($tableName, 'level_id')) $table->string('level_id');
            if(!Schema::hasColumn($tableName, 'level_unit_id')) $table->string('level_unit_id')->nullable();
            foreach ($exam->subjects as $subject) {
                if(!Schema::hasColumn($tableName, $subject->shortname)) $table->jsonb($subject->shortname)->nullable();
            }
            if(!Schema::hasColumn($tableName, 'mp')) $table->tinyInteger('mp', false, true)->nullable();
            if(!Schema::hasColumn($tableName, 'tp')) $table->tinyInteger('tp', false, true)->nullable();
            if(!Schema::hasColumn($tableName, 'mg')) $table->char('mg', 2)->nullable();
            if(!Schema::hasColumn($tableName, 'mm')) $table->double('mm')->nullable();
            if(!Schema::hasColumn($tableName, 'tm')) $table->integer('tm', false, true)->nullable();
            if(!Schema::hasColumn($tableName, 'sp')) $table->integer('sp')->nullable();
            if(!Schema::hasColumn($tableName, 'op')) $table->integer('op')->nullable();
            if(!Schema::hasColumn($tableName, 'mmd')) $table->double('mmd')->nullable();
            if(!Schema::hasColumn($tableName, 'tmd')) $table->double('tmd')->nullable();
            if(!Schema::hasColumn($tableName, 'tpd')) $table->integer('tpd')->nullable();
            if(!Schema::hasColumn($tableName, 'mpd')) $table->integer('mpd')->nullable();
        });
        
    }
    
}
