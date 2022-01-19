<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use Livewire\Livewire;
use App\Models\Grading;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Exam\CreateScoresTable;
use App\Http\Livewire\LevelUnitExamScores;
use App\Models\Grade;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelUnitsExamsManagmentTest extends TestCase
{    use RefreshDatabase, WithFaker;

    /** @var Teacher */
    private Teacher $teacher;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        $this->teacher = Teacher::factory()->create();

        /** @var Authenticatable */
        $user = $this->teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'role_id' => $this->role->id,
            'password' => Hash::make('password')
        ]);
        
        $this->actingAs($user);
        
    }

    /** @group exam-scores */
    public function testAuthorizedUsersCanGenerateAggregatesForTheWholeClass()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        // Create Responsibility for the current teacher
        $classTeacherResponsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($classTeacherResponsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table
        CreateScoresTable::invoke($exam);

        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                    ->updateOrInsert(["student_id" => $student->id], [
                        $subject->shortname => json_encode([
                                'score' => $this->faker->numberBetween(0, 100),
                                'grade' => $this->faker->randomElement(Grading::gradeOptions()),
                                'points' => $this->faker->numberBetween(0, 12),
                        ]),
                        'level_id' => $levelUnit->level->id,
                        'level_unit_id' => $levelUnit->id
                    ]);
    
            }

        }

        Livewire::test(LevelUnitExamScores::class, [
            'exam' => $exam,
            'levelUnit' => $levelUnit
        ])->call('generateBulkAggregates');

        $tblName = Str::slug($exam->shortname);

        $this->assertEquals(2, DB::table($tblName)->count());

        $data = DB::table($tblName)
            ->select(["mm", "tm", "mp", "tp", "mg"])
            ->where('level_unit_id', $levelUnit->id)
            ->get();

        foreach ($data as $item) {
            $this->assertTrue(!is_null($item->tm));
            $this->assertTrue(!is_null($item->mp));
            $this->assertTrue(!is_null($item->tp));
            $this->assertTrue(!is_null($item->mg));
            $this->assertTrue(!is_null($item->mm));
        }
    }


    /** @group exam-scores */
    public function testAuthorizedUsersCanGenerateAggregatesForASingleStudent()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        // Create Responsibility for the current teacher
        $classTeacherResponsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($classTeacherResponsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table

        CreateScoresTable::invoke($exam);

        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                ->updateOrInsert([
                    "student_id" => $student->id
                ], [
                    $subject->shortname => json_encode([
                            'score' => $this->faker->numberBetween(0, 100),
                            'grade' => $this->faker->randomElement(Grading::gradeOptions()),
                            'points' => $this->faker->numberBetween(0, 12),
                    ]),
                    'level_id' => $levelUnit->level->id,
                    'level_unit_id' => $levelUnit->id
                ]);
    
            }

        }

        /** @var Student */
        $firstStudent = Student::first();

        Livewire::test(LevelUnitExamScores::class, [
            'exam' => $exam,
            'levelUnit' => $levelUnit
        ])->call('showGenerateAggregatesModal', $firstStudent->id)->call('generateAggregates');

        $tblName = Str::slug($exam->shortname);

        $data = DB::table($tblName)->where('student_id', $firstStudent->id)->select(["mm", "tm", "mp", "tp", "mg"])->first();
        
        $this->assertTrue(!is_null($data->tm));
        $this->assertTrue(!is_null($data->mp));
        $this->assertTrue(!is_null($data->tp));
        $this->assertTrue(!is_null($data->mg));
        $this->assertTrue(!is_null($data->mm));
    }    


    /** @group exam-scores */
    public function testClassTeacherCanPublishClassScores()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        // Create Responsibility for the current teacher
        $classTeacherResponsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($classTeacherResponsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table
        CreateScoresTable::invoke($exam, true);

        // Adding scores for the students
        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                    ->updateOrInsert(["student_id" => $student->id], [
                        $subject->shortname => json_encode([
                            'score' => $this->faker->numberBetween(0, 100),
                            'grade' => $grade = $this->faker->randomElement(Grading::gradeOptions()),
                            'points' => $this->faker->numberBetween(0, 12),
                        ]),
                        'level_id' => $levelUnit->level->id,
                        'level_unit_id' => $levelUnit->id
                    ]);
    
            }

        }

        // Calculating and recording aggregates

        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_unit_id", $levelUnit->id)
            ->select(array_merge(["student_id"], $cols))->get();

        $data->each(function($stuData) use($tblName, $cols){

            $totalScore = 0;
            $totalPoints = 0;
            $populatedCols = 0;

            foreach ($cols as $col) {

                if(!is_null($stuData->$col)){
                    $populatedCols++;

                    $subData = json_decode($stuData->$col);

                    $totalScore += $subData->score ?? 0;
                    $totalPoints += $subData->points ?? 0;
                }
            }

            $avgPoints = round($totalPoints / $populatedCols);
            $avgScore = round($totalScore / $populatedCols);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
                ->updateOrInsert(["student_id" => $stuData->student_id], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    "tp" => $totalPoints,
                    'tm' => $totalScore
                ]);
        });

        // Acting

        $response = Livewire::test(LevelUnitExamScores::class, ['exam' =>$exam, 'levelUnit' => $levelUnit])->call('publishClassScores');

        // Assertions
        $dbDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($dbDriver === 'mysql') {
            
            $this->assertEquals(1, $exam->levelUnits()->count());
            
            $levelUnitWithScore = $exam->levelUnits->first();
    
            $this->assertNotNull($levelUnitWithScore->pivot->points);
    
            $this->assertNotNull($levelUnitWithScore->pivot->grade);
    
            $this->assertNotNull($levelUnitWithScore->pivot->average);

        }else{

            $response->assertHasErrors('error');

        }

    }

    /** @group exam-scores */
    public function testClassTeacherCanRankStudents()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        // Create Responsibility for the current teacher
        $classTeacherResponsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($classTeacherResponsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table
        CreateScoresTable::invoke($exam, true);

        // Adding scores for the students
        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                    ->updateOrInsert(["student_id" => $student->id], [
                        $subject->shortname => json_encode([
                            'score' => $this->faker->numberBetween(0, 100),
                            'grade' => $grade = $this->faker->randomElement(Grading::gradeOptions()),
                            'points' => $this->faker->numberBetween(0, 12),
                        ]),
                        'level_id' => $levelUnit->level->id,
                        'level_unit_id' => $levelUnit->id
                    ]);
    
            }

        }

        // Calculating and recording aggregates

        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_unit_id", $levelUnit->id)
            ->select(array_merge(["student_id"], $cols))->get();

        $data->each(function($stuData) use($tblName, $cols){

            $totalScore = 0;
            $totalPoints = 0;
            $populatedCols = 0;

            foreach ($cols as $col) {

                if(!is_null($stuData->$col)){
                    $populatedCols++;

                    $subData = json_decode($stuData->$col);

                    $totalScore += $subData->score ?? 0;
                    $totalPoints += $subData->points ?? 0;
                }
            }

            $avgPoints = round($totalPoints / $populatedCols);
            $avgScore = round($totalScore / $populatedCols);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
                ->updateOrInsert(["student_id" => $stuData->student_id], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    "tp" => $totalPoints,
                    'tm' => $totalScore
                ]);
        });

        // Action => Call the student ranking method

        $col = 'tm';

        Livewire::test(LevelUnitExamScores::class, ['exam' =>$exam, 'levelUnit' => $levelUnit])
            ->set('col', $col)
            ->call('generateRanks');

        // Assertions
        $tblName = Str::slug($exam->shortname);

        $firstRecordInRank = DB::table($tblName)
            ->where('level_unit_id', $levelUnit->id)
            ->select(['student_id', $col, 'sp'])
            ->orderBy($col, 'desc')
            ->first();

        $this->assertEquals(1, $firstRecordInRank->sp);
    }

    /** @group exam-scores */
    public function testAuthorizedTeacherCanPublishExamLevelUnitGradeDistribution()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        $responsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table
        CreateScoresTable::invoke($exam);

        // Upload students scores
        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                ->updateOrInsert(["student_id" => $student->id], [
                    $subject->shortname => json_encode([
                            'score' => $this->faker->numberBetween(0, 100),
                            'grade' => $this->faker->randomElement(Grading::gradeOptions()),
                            'points' => $this->faker->numberBetween(0, 12),
                    ]),
                    'level_id' => $levelUnit->level->id,
                    'level_unit_id' => $levelUnit->id
                ]);
    
            }

        }
        
        // Generate students aggregates
        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_id", $levelUnit->level->id)
            ->select(array_merge(["student_id"], $cols))->get();

        $data->each(function($stuData) use($tblName, $cols){
            $totalScore = 0;
            $totalPoints = 0;
            $populatedCols = 0;

            foreach ($cols as $col) {

                if(!is_null($stuData->$col)){
                    $populatedCols++;

                    $subData = json_decode($stuData->$col);

                    $totalScore += $subData->score ?? 0;
                    $totalPoints += $subData->points ?? 0;
                }
            }

            $avgPoints = round($totalPoints / $populatedCols);
            $avgScore = round($totalScore / $populatedCols);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
            ->updateOrInsert([
                "student_id" => $stuData->student_id
            ], [
                "mm" => $avgScore,
                "mg" => $avgGrade,
                'mp' => $avgPoints,
                'tp' => $totalPoints,
                'tm' => $totalScore
            ]);
        });

        // Call the grade distribution method
        Livewire::test(LevelUnitExamScores::class, ['exam' =>$exam, 'levelUnit' => $levelUnit])
            ->call('publishLevelUnitGradeDistribution');

        $this->assertEquals(count(Grading::gradeOptions()), $exam->levelUnitGradesDistribution()->count());
        
    }

    /** @group exam-scores */
    public function _testClassTeacherCanPublishExamLevelUnitSubjectPerformance()
    {        
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        $responsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table
        CreateScoresTable::invoke($exam);

        // Upload students scores
        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                ->updateOrInsert([
                    "admno" => $student->adm_no
                ], [
                    $subject->shortname => json_encode([
                            'score' => $this->faker->numberBetween(0, 100),
                            'grade' => $this->faker->randomElement(Grading::gradeOptions()),
                            'points' => $this->faker->numberBetween(0, 12),
                    ]),
                    'level_id' => $levelUnit->level->id,
                    'level_unit_id' => $levelUnit->id
                ]);
    
            }

        }
        
        // Generate Aggregates
        
        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_unit_id", $levelUnit->id)
            ->select(array_merge(["admno"], $cols))->get();

        $data->each(function($stuData) use($tblName, $cols){
            $totalScore = 0;
            $totalPoints = 0;
            $populatedCols = 0;

            foreach ($cols as $col) {

                if(!is_null($stuData->$col)){
                    $populatedCols++;

                    $subData = json_decode($stuData->$col);

                    $totalScore += $subData->score ?? 0;
                    $totalPoints += $subData->points ?? 0;
                }
            }

            $avgPoints = round($totalPoints / $populatedCols);
            $avgScore = round($totalScore / $populatedCols);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
            ->updateOrInsert([
                "admno" => $stuData->admno
            ], [
                "mm" => $avgScore,
                "mg" => $avgGrade,
                'mp' => $avgPoints,
                'tp' => $totalPoints,
                'tm' => $totalScore
            ]);
        });

        Livewire::test(LevelUnitExamScores::class, ['exam' =>$exam, 'levelUnit' => $levelUnit])
            ->call('publishLevelUnitSubjectPerformance');

        $this->assertEquals(2, $exam->levelUnitSubjectPerformance()->count());
    
    }

    /** @group exam-scores */
    public function testAuthorizedUserCanPublishTopSubjectStudents()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        // Create Responsibility for the current teacher
        $classTeacherResponsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($classTeacherResponsibility, [
            'level_unit_id' => $levelUnit->id,
        ]);        

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subjects);

        // Create Scores Table
        CreateScoresTable::invoke($exam, true);

        // Adding scores for the students
        foreach ($students as $student) {

            foreach ($subjects as $subject) {

                DB::table(Str::slug($exam->shortname))
                    ->updateOrInsert(["student_id" => $student->id], [
                        $subject->shortname => json_encode([
                            'score' => $this->faker->numberBetween(0, 100),
                            'grade' => $grade = $this->faker->randomElement(Grading::gradeOptions()),
                            'points' => $this->faker->numberBetween(0, 12),
                        ]),
                        'level_id' => $levelUnit->level->id,
                        'level_unit_id' => $levelUnit->id
                    ]);
    
            }

        }

        // Calculating and recording aggregates

        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_unit_id", $levelUnit->id)
            ->select(array_merge(["student_id"], $cols))->get();

        $data->each(function($stuData) use($tblName, $cols){

            $totalScore = 0;
            $totalPoints = 0;
            $populatedCols = 0;

            foreach ($cols as $col) {

                if(!is_null($stuData->$col)){
                    $populatedCols++;

                    $subData = json_decode($stuData->$col);

                    $totalScore += $subData->score ?? 0;
                    $totalPoints += $subData->points ?? 0;
                }
            }

            $avgPoints = round($totalPoints / $populatedCols);
            $avgScore = round($totalScore / $populatedCols);

            $pgm = Grade::all(['points', 'grade'])->pluck('grade', 'points');

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
                ->updateOrInsert(["student_id" => $stuData->student_id], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    "tp" => $totalPoints,
                    'tm' => $totalScore
                ]);
        });

        // Act
        $response = Livewire::test(LevelUnitExamScores::class, ['exam' => $exam, 'levelUnit' => $levelUnit])
            ->call('publishTopStudentsSubjectWise');

        $dbDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if ($dbDriver == 'mysql') {
            $this->assertTrue(DB::table('exam_level_unit_top_students_per_subject')->count() > 0);
        } else {
            $response->assertHasErrors(['error']);
        } 
    }

}
