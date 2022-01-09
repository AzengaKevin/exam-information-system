<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Grade;
use Livewire\Livewire;
use App\Models\Grading;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Livewire\LevelExamScores;
use App\Actions\Exam\CreateScoresTable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelsExamsManagementTest extends TestCase
{    
    use RefreshDatabase, WithFaker;

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
    public function testAuthorizedTeacherCanVisitLevelManageExamScores()
    {
        $this->withoutExceptionHandling();

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        // Create the Subject
        $subject = Subject::factory()->create();

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $levelUnit->level->id
        ]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $response = $this->get(route('exams.scores.manage', [
            'exam' => $exam,
            'level' => $levelUnit->level->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.scores.manage');

        $response->assertViewHasAll(['exam', 'level', 'title']);

        $response->assertSeeLivewire('level-exam-scores');
        
    }

    /** @group exam-scores */
    public function testLevelSupervisorCanPublishLevelScores()
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

        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $levelUnit->level->id,
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

        Livewire::test(LevelExamScores::class, ['exam' =>$exam, 'level' => $levelUnit->level])
            ->call('publishLevelScores');

        $this->assertEquals(1, $exam->levels()->count());

        $levelWithScore = $exam->levels()
            ->where('levels.id', $levelUnit->level->id)
            ->first();

        $this->assertNotNull($levelWithScore);

        $this->assertNotNull($levelWithScore->pivot->points);

        $this->assertNotNull($levelWithScore->pivot->grade);

        $this->assertNotNull($levelWithScore->pivot->average);
    }

    /** @test exam-scores */
    public function testLevelSupervisorCanPublishExamLevelGradeDistribution()
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

        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $levelUnit->level->id,
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

        Livewire::test(LevelExamScores::class, ['exam' =>$exam, 'level' => $levelUnit->level])
            ->call('publishLevelGradeDistribution');

        $this->assertEquals(count(Grading::gradeOptions()), $exam->levelGradesDist()->count());
        
    }

    /** @group exam-scores */
    public function _testLevelSupervisorCanPublishExamLevelSubjectPerformance()
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

        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $levelUnit->level->id,
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

        Livewire::test(LevelExamScores::class, ['exam' =>$exam, 'level' => $levelUnit->level])
            ->call('publishLevelSubjectPerformance');

        $this->assertEquals(2, $exam->levelSubjectPerformance()->count());
    
    }

    /** @group exam-scores */
    public function testAuthorizedTeacherCanRankLevelStudents()
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

        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $levelUnit->level->id,
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

            DB::table($tblName)->updateOrInsert(["student_id" => $stuData->student_id], [
                "mm" => $avgScore,
                "mg" => $avgGrade,
                'mp' => $avgPoints,
                'tp' => $totalPoints,
                'tm' => $totalScore
            ]);

        });

        // Action => Call the student ranking method

        $col = 'tm';

        Livewire::test(LevelExamScores::class, ['exam' =>$exam, 'level' => $levelUnit->level])
            ->set('col', $col)
            ->call('generateRanks');
    
        // Assertions
        $tblName = Str::slug($exam->shortname);

        $firstRecordInRank = DB::table($tblName)
            ->where('level_id', $levelUnit->level->id)
            ->select(['student_id', $col, 'op'])
            ->orderBy($col, 'desc')
            ->first();

        $this->assertEquals(1, $firstRecordInRank->op);
        
    }

    /** @group exam-scores */
    public function testAuthorizedTeacherCanPublishStudentResults()
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

        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $levelUnit->level->id,
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
        
        // Generating students aggregates

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

            DB::table($tblName)->updateOrInsert(["student_id" => $stuData->student_id], [
                "mm" => $avgScore,
                "mg" => $avgGrade,
                'mp' => $avgPoints,
                'tp' => $totalPoints,
                'tm' => $totalScore
            ]);

        });

        // Rank student by level unit
        $col = 'tm';

        $tblName = Str::slug($exam->shortname);

        // Get order records from the databas with the student_id number as the primary key

        /** @var Collection */
        $data = DB::table($tblName)
            ->select(['student_id', $col])
            ->where('level_unit_id', $levelUnit->id)
            ->orderBy($col, 'desc')
            ->get();

        $prevRank = -1;
        $currRank = -1;
        $prevVal = 0;
        $currVal = 0;

        foreach ($data as $key => $record) {

            if($key == 0) $currRank = 1;

            $currVal = $record->$col;

            if($key != 0){
                if($prevVal == $currVal){
                    $currRank = $prevRank;
                }
            }

            DB::table($tblName)->updateOrInsert(['student_id' => $record->student_id],['sp' => $currRank]);

            $prevVal = $currVal;

            $prevRank = $currRank;

            ++$currRank;
        }

        // Rank students by level

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->select(['student_id', $col])
            ->where('level_id', $levelUnit->level->id)
            ->orderBy($col, 'desc')
            ->get();

        $prevRank = -1;
        $currRank = -1;
        $prevVal = 0;
        $currVal = 0;

        foreach ($data as $key => $record) {

            if($key == 0) $currRank = 1;

            $currVal = $record->$col;

            if($key != 0){
                if($prevVal == $currVal){
                    $currRank = $prevRank;
                }
            }

            DB::table($tblName)->updateOrInsert(['student_id' => $record->student_id],[
                'op' => $currRank
            ]);

            $prevVal = $currVal;

            $prevRank = $currRank;

            ++$currRank;
        }
        
        // Action

        Livewire::test(LevelExamScores::class, ['exam' =>$exam, 'level' => $levelUnit->level])
            ->call('publishStudentResults');

        $this->assertEquals($students->count(), $exam->students()->count());
    }
}
