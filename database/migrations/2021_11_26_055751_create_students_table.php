<?php

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('adm_no')->nullable()->unique();
            $table->string('upi')->nullable();
            $table->integer('kcpe_marks')->nullable();
            $table->enum('kcpe_grade', Student::kcpeGradeOptions())->nullable();
            $table->enum('gender', User::genderOptions())->nullable();
            $table->date('dob')->nullable();
            $table->foreignId('level_id')->nullable()->constrained();
            $table->foreignId('stream_id')->nullable()->constrained();
            $table->unsignedBigInteger('hostel_id')->nullable();
            $table->foreignId('admission_level_id')->nullable()->constrained('levels');
            $table->mediumText('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students');
    }
}
