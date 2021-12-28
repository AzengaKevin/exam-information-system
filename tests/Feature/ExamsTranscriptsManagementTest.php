<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Level;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Exam\CreateScoresTable;
use App\Models\Grade;
use App\Models\Grading;
use App\Models\Responsibility;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamsTranscriptsManagementTest extends TestCase
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
    
    /** @group exams-transcripts */
    public function testAuthorizedTeacherCanVisitProvisionalTranscriptsPage()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();
        
        Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);
        
        $exam->subjects()->attach($subjects);

        $response = $this->get(route('exams.transcripts.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.transcripts.index');

        $response->assertViewHasAll(['levelUnits', 'exam', 'systemSettings']);
        
    }

    /** @group exams-transcripts */
    public function testAuthorizedUsersCanSeeTranscriptsForTheWholeLevelUnit()
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
                    ->updateOrInsert(["admno" => $student->adm_no], [
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

        // Calculating and recording aggregates
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
                ->updateOrInsert(["admno" => $stuData->admno], [
                    "mm" => $avgScore,
                    "mg" => $avgGrade,
                    'mp' => $avgPoints,
                    "tp" => $totalPoints,
                    'tm' => $totalScore
                ]);

            });
            
        $response = $this->get(route('exams.transcripts.show',[
            'exam' =>  $exam,
            'level-unit' => $levelUnit->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.transcripts.show');

        $response->assertViewHasAll([
            'exam', 
            'levelUnit', 
            'studentsScores', 
            'subjectColumns', 
            'subjectsMap', 
            'swahiliComments', 
            'englishComments', 
            'teachers', 
            'outOfs', 
            'title',
            'systemSettings',
            'generalSettings'
        ]);
        
    }

    /** @group exams-scores */
    public function testAuthorizedUsersCanSeeTranscriptsForTheWholeLevel()
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

        $response = $this->get(route('exams.transcripts.show',[
            'exam' =>  $exam,
            'level' => $level->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.transcripts.show');

        $response->assertViewHasAll([
            'exam', 
            'level', 
            'studentsScores', 
            'subjectColumns', 
            'subjectsMap', 
            'swahiliComments', 
            'englishComments', 
            'teachers', 
            'outOfs', 
            'title',
            'systemSettings',
            'generalSettings'
        ]);
        
    }

    /** @group exams-transcripts */
    public function _testAuthorizedUserCanSeeStudentProvisionalTranscript()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');
        $this->artisan('db:seed --class=LevelsSeeder');
        $this->artisan('db:seed --class=StreamsSeeder');

        $stream = Stream::first();

        $level = Level::first();

        DB::table('level_units')
        ->updateOrInsert([
            'stream_id' => $stream->id,
            'level_id' => $level->id
        ],[
            'alias' => "{$level->numeric}{$stream->alias}"
        ]);

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::first();
        
        Student::factory(2)->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subjects = Subject::limit(2)->get();

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);
        
        $exam->subjects()->attach($subjects);

        /** @var Student */
        $student = Student::first();

        CreateScoresTable::invoke($exam);
        
        $response = $this->get(route('exams.transcripts.index', [
            'exam' => $exam,
            'admno' => $student->adm_no
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.transcripts.index');

        $response->assertViewHasAll(['students', 'exam', 'level', 'levelUnit']);
        
    }
}
