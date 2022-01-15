<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviationColumnsToExamLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_level', function (Blueprint $table) {
            $table->double('points_deviation', 6, 4)->nullable();
            $table->double('average_deviation', 6, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_level', function (Blueprint $table) {
            $table->dropColumn(['points_deviation', 'average_deviation']);
        });
    }
}
