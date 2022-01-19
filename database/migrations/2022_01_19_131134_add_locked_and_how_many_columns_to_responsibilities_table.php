<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLockedAndHowManyColumnsToResponsibilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('responsibilities', function (Blueprint $table) {
            $table->boolean('locked')->nullable()->default(false);
            $table->tinyInteger('how_many')->nullable()->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('responsibilities', function (Blueprint $table) {
            $table->dropColumn(['locked', 'how_many']);
        });
    }
}
