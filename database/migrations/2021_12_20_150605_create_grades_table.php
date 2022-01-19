<?php

use App\Models\Grading;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->enum('grade', Grading::gradeOptions())->unique();
            $table->tinyInteger('points', false, true);
            $table->mediumText('swahili_comment');
            $table->mediumText('english_comment');
            $table->mediumText('ct_comment');
            $table->mediumText('p_comment');
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('grades');
    }
}
