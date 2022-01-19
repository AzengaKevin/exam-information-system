<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Grade;
use App\Models\Grading;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Exam\CreateScoresTable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamsResultsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @var Teacher */
    private $teacher;

    private $role;

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

    /** @group exam-results */
    public function testExamResultsPageCanBeVisited()
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
        $this->uploadStudentScores($students, $subjects, $exam, $levelUnit);

        // Generate Aggregates for exam scores
        $this->generateAggregatesForExamScores($exam, $levelUnit);

        $response = $this->get(route('exams.results.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.results.index');

        $response->assertViewHasAll(['exam', 'level', 'levelUnit']);

        $response->assertSeeLivewire('level-exam-results');
        
    }
    
    /** @group exam-results */
    public function testLevelExamResultsPageCanBeVisited()
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
        $this->uploadStudentScores($students, $subjects, $exam, $levelUnit);

        // Generate Aggregates for exam scores
        $this->generateAggregatesForExamScores($exam, $levelUnit);

        $response = $this->get(route('exams.results.index', [
            'exam' => $exam,
            'level' => $levelUnit->level->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.results.index');

        $response->assertViewHasAll(['exam', 'level', 'levelUnit']);

        $response->assertSeeLivewire('level-unit-exam-results');
        
    }

    /**
     * Helper fuction to upload student scores and optimize exam results testing code
     * 
     * @param Collection $students
     * @param Collection $subjects
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     */
    private function uploadStudentScores(Collection $students, Collection $subjects, Exam $exam, LevelUnit $levelUnit)
    {
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
    }

    /**
     * Generate aggregates for the uploaded scores
     * 
     * @param Exam $exam
     * @param LevelUnit $levelUnit
     * 
     */
    private function generateAggregatesForExamScores(Exam $exam, LevelUnit $levelUnit)
    {
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
    }
}
