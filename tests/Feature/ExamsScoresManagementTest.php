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
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
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

        $response->assertViewHasAll(['exam', 'responsibilities', 'systemSettings']);
        
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

    /** @group exams-scores */
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

    /** @group exams-scores */
    public function testAuthorizedUserCanUpdateAnExamScoresTable()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');

        $subjects = Subject::all();
        
        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->subjects()->attach($subjects);

        Livewire::test(ExamQuickActions::class, ['exam' => $exam])
            ->call('updateScoresTable');

        $this->assertTrue(Schema::hasTable(Str::slug($exam->shortname)));
        
    }
    
    
}
