<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviationColumnsToExamStudentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_student', function (Blueprint $table) {
            $table->double('mmd')->nullable();
            $table->double('tmd')->nullable();
            $table->integer('tpd')->nullable();
            $table->integer('mpd')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_student', function (Blueprint $table) {
            $table->dropColumn(['mmd', 'tmd', 'tpd', 'mpd']);
        });
    }
}
