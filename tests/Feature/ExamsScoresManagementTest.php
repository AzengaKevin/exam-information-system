<?php

namespace Tests\Feature;

use App\Actions\Exam\CreateScoresTable;
use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Http\Livewire\ExamQuickActions;
use App\Http\Livewire\LevelUnitExamScores;
use App\Models\Grading;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class ExamsScoresManagementTest extends TestCase
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
            'role_id' => $this->role->id,
            'password' => Hash::make('password')
        ]);
        
        $this->actingAs($user);
        
    }

    /** @group exams-scores */
    public function testAuthorizedUserCanVisitExamScoresPageAndViewOwnClasses()
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()->create();

        $response = $this->get(route('exams.scores.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.scores.index');

        $response->assertViewHasAll(['exam', 'responsibilities']);
        
    }

    /** @group exams-scores */
    public function testAuthorizedUserCanVisitPageToUploadSubjectScores()
    {
        $this->withoutExceptionHandling();

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        // Create the Subject
        $subject = Subject::factory()->create();

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_unit_id' => $levelUnit->id,
            'subject_id' => $subject->id
        ]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $response = $this->get(route('exams.scores.create', [
            'exam' => $exam,
            'subject' => $subject->id,
            'level-unit' => $levelUnit->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.scores.create');

        $response->assertViewHasAll(['exam', 'subject', 'levelUnit', 'gradings']);
        
    }

    /** @group exams */
    public function testAuthorizedTeacherCanVisitPageToGenerateAggregates()
    {
        $this->withoutExceptionHandling();

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        // Create the Subject
        $subject = Subject::factory()->create();

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_unit_id' => $levelUnit->id
        ]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $response = $this->get(route('exams.scores.create', [
            'exam' => $exam,
            'level-unit' => $levelUnit->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.scores.create');

        $response->assertViewHasAll(['exam', 'subject', 'levelUnit', 'gradings']);

        $response->assertSeeLivewire('level-unit-exam-scores');
        
    }    

    /** @group exams-scores */
    public function testAuthorizedTeacherCanUploadScores()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        /** @var Student */
        $student = Student::factory()->create([
            'admission_level_id' => $levelUnit->level->id,
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);

        // Create the Subject
        $subject = Subject::factory()->create();

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_unit_id' => $levelUnit->id,
            'subject_id' => $subject->id
        ]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($levelUnit->level);

        $exam->subjects()->attach($subject);

        // Create Scores Table
        CreateScoresTable::invoke($exam);

        $payload = array(
            'scores' => array(
                $student->adm_no => 85
            )
        );

        $response = $this->post(route('exams.scores.store', [
            'exam' => $exam,
            'subject' => $subject->id,
            'level-unit' => $levelUnit->id
        ]), $payload);

        $col = $subject->shortname;

        $this->assertEquals(1, DB::table(Str::slug($exam->shortname))->count());

        $this->assertEquals(85, json_decode(DB::table(Str::slug($exam->shortname))->first()->$col, true)['score']);
        
    }

    /** @group exams-stores */
    public function testAuthorizedUsersCanGenerateAggregatesForTheWholeClass()
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

        Livewire::test(LevelUnitExamScores::class, [
            'exam' => $exam,
            'levelUnit' => $levelUnit
        ])->call('generateBulkAggregates');

        $tblName = Str::slug($exam->shortname);

        $this->assertEquals(2, DB::table($tblName)->count());

        $data = DB::table($tblName)->select(["average", "total"])->get();

        foreach ($data as $item) {
            $this->assertTrue(!is_null($item->total));
            $this->assertTrue(is_array(json_decode($item->average, true)));
        }
    }


    /** @group exams-stores */
    public function testAuthorizedUsersCanGenerateAggregatesForASingleStudent()
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

        Livewire::test(LevelUnitExamScores::class, [
            'exam' => $exam,
            'levelUnit' => $levelUnit
        ])->call('showGenerateAggregatesModal', $students->first()->adm_no)
            ->call('generateAggregates');

        $tblName = Str::slug($exam->shortname);

        $data = DB::table($tblName)->where('admno', $students->first()->adm_no)->select(["average", "total"])->first();

        $this->assertTrue(!is_null($data->total));
        $this->assertTrue(is_array(json_decode($data->average, true)));
    }    

    /** @group exams-scores */
    public function testAuthorizedUserCanCreateAScoresTable()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');

        $subjects = Subject::all();
        
        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->subjects()->attach($subjects);

        Livewire::test(ExamQuickActions::class, ['exam' => $exam])
            ->call('createScoresTable');

        $this->assertTrue(Schema::hasTable(Str::slug($exam->shortname)));
        
    }
    
    
}
