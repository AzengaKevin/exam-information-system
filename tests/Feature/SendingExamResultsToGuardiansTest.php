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
use App\Models\Guardian;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class SendingExamResultsToGuardiansTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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

    /**
     * @group exams-results
     */
    public function testAuthorizedUserCanSendLevelUnitExamResults()
    {
        
        $this->withoutExceptionHandling();

        Notification::fake();

        $this->artisan('db:seed --class=GradingSeeder');
        $this->artisan('db:seed --class=GradeSeeder');
        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        /** @var Collection */
        $students = Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Attach guardians for the students

        $students->each(function(Student $student){
            
            /** @var Guardian */
            $guardian = Guardian::factory()->create();

            $guardian->auth()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->safeEmail(),
                'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                'password' => Hash::make('password')
            ]);

            $student->guardians()->attach($guardian);
            
        });

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
        
        // Generating students aggregates

        $cols = $exam->subjects->pluck("shortname")->toArray();

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->where("level_id", $levelUnit->level->id)
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

            DB::table($tblName)->updateOrInsert(["admno" => $stuData->admno], [
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

        // Get order records from the databas with the admno number as the primary key

        /** @var Collection */
        $data = DB::table($tblName)
            ->select(['admno', $col])
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

            DB::table($tblName)->updateOrInsert(['admno' => $record->admno],['sp' => $currRank]);

            $prevVal = $currVal;

            $prevRank = $currRank;

            ++$currRank;
        }

        // Rank students by level

        $tblName = Str::slug($exam->shortname);

        /** @var Collection */
        $data = DB::table($tblName)
            ->select(['admno', $col])
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

            DB::table($tblName)->updateOrInsert(['admno' => $record->admno],[
                'op' => $currRank
            ]);

            $prevVal = $currVal;

            $prevRank = $currRank;

            ++$currRank;
        }

        $response = $this->post(route('exams.results.send-message', [
            'exam' => $exam,
            'level_unit_id' => $levelUnit->id
        ]));

        $this->assertEquals($students->count(), Message::count());

        $response->assertRedirect();

    }
    
}
