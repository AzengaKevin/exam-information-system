<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Level;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Exam\CreateScoresTable;
use App\Http\Livewire\LevelExamScores;
use App\Models\Grade;
use App\Models\Grading;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class LevelsExamsScoresManagementTest extends TestCase
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
            'role_id' => $this->role->id,
            'password' => Hash::make('password')
        ]);
        
        $this->actingAs($user);
        
    }

    /** @group exams-scores  */
    public function testLevelExamScoresManagementPageCanBeVisitedByAuthorizedTeacher()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var Level */
        $level = Level::factory()->create();

        /** @var Collection */
        $students = Student::factory(2)->create([
            'admission_level_id' => $level->id,
            'stream_id' => null,
            'level_unit_id' => null
        ]);

        /** @var Collection */
        $subjects = Subject::limit(2)->get();

        $responsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, ['level_id' => $level->id,]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($level);

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
                    'level_id' => $level->id,
                ]);
    
            }

        }
            
        // Generating level aggregates
        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_id", $level->id)
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

        $response = $this->get(route('exams.scores.manage', [
            'exam' => $exam,
            'level' => $level->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.scores.manage');

        $response->assertSeeLivewire('level-exam-scores');

        $response->assertViewHasAll(['exam', 'level']);
        
    }

    /** @group exams-scores */
    public function testAuthorizedTeacherCanGenerateLevelScoresAggregates()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var Level */
        $level = Level::factory()->create();

        /** @var Collection */
        $students = Student::factory(2)->create([
            'admission_level_id' => $level->id,
            'stream_id' => null,
            'level_unit_id' => null
        ]);

        /** @var Collection */
        $subjects = Subject::limit(2)->get();

        $responsibility = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, ['level_id' => $level->id,]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($level);

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
                    'level_id' => $level->id,
                ]);
    
            }

        }
        
        Livewire::test(LevelExamScores::class, [
            'exam' => $exam,
            'level' => $level
        ])->call('generateBulkLevelAggregates');

        $tblName = Str::slug($exam->shortname);

        $this->assertEquals(2, DB::table($tblName)->count());

        $data = DB::table($tblName)
            ->select(["mm", "tm", "mp", "tp", "mg"])
            ->where('level_id', $level->id)
            ->get();

        foreach ($data as $item) {
            $this->assertTrue(!is_null($item->tm));
            $this->assertTrue(!is_null($item->mp));
            $this->assertTrue(!is_null($item->tp));
            $this->assertTrue(!is_null($item->mg));
            $this->assertTrue(!is_null($item->mm));
        }
        
    }

}
