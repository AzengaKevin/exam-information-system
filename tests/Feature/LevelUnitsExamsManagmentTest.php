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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelUnitsExamsManagmentTest extends TestCase
{    use RefreshDatabase, WithFaker;

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
            'role_id' => $this->role->id,
            'password' => Hash::make('password')
        ]);
        
        $this->actingAs($user);
        
    }

    /** @group scores */
    public function testClassTeacherCanPublishClassScores()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
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

            $pgm = Grading::pointsGradeMap();

            $avgGrade = $pgm[$avgPoints];

            DB::table($tblName)
            ->updateOrInsert([
                "admno" => $stuData->admno
            ], [
                "average" => $avgScore,
                "grade" => $avgGrade,
                'points' => $avgPoints,
                'total' => $totalScore
            ]);
        });

        Livewire::test(LevelUnitExamScores::class, ['exam' =>$exam, 'levelUnit' => $levelUnit])
            ->call('publishClassScores');

        $this->assertEquals(1, $exam->levelUnits()->count());
        
        $levelUnitWithScore = $exam->levelUnits->first();

        $this->assertNotNull($levelUnitWithScore->pivot->points);

        $this->assertNotNull($levelUnitWithScore->pivot->grade);

        $this->assertNotNull($levelUnitWithScore->pivot->average);
    }

}
