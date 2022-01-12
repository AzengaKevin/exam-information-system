<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Level;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Actions\Exam\CreateScoresTable;
use App\Http\Livewire\ExamQuickActions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /** @group exam-scores */
    public function testLevelSupervisorCanVisitExamScoresPageWhenApplicable()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');
    
        /** @var Level */
        $level = Level::factory()->create();

        Student::factory(2)->create([
            'adm_no' => null,
            'kcpe_marks' => null,
            'kcpe_grade' => null,
            'stream_id' => null,
            'admission_level_id' => $level->id,
        ]);

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Level Supervisor']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $level->id,
        ]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($level);

        $subjects = Subject::limit(2)->get();

        $exam->subjects()->attach($subjects);

        CreateScoresTable::invoke($exam);

        $exam->update(['status' => 'Marking']);

        $response = $this->get(route('exams.scores.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.scores.index');

        $response->assertViewHasAll(['exam', 'responsibilities', 'systemSettings']);
        
    }

    /** @group exam-scores */
    public function testDosCanVisitExamScoresPageWhenApplicable()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');
    
        /** @var Level */
        $level = Level::factory()->create();

        Student::factory(2)->create([
            'adm_no' => null,
            'kcpe_marks' => null,
            'kcpe_grade' => null,
            'stream_id' => null,
            'admission_level_id' => $level->id,
        ]);

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Director of Studies']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($level);

        $subjects = Subject::limit(2)->get();

        $exam->subjects()->attach($subjects);

        CreateScoresTable::invoke($exam);

        $exam->update(['status' => 'Marking']);

        $response = $this->get(route('exams.scores.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.scores.index');

        $response->assertViewHasAll(['exam', 'responsibilities', 'systemSettings']);
        
    }

    /** @group exam-scores */
    public function testAuthorizedTeacherCanVisitPageToUploadLevelUnitSubjectScores()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');

        // Create the Level Unit
        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        // Create students
        Student::factory()->create([
            'admission_level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
        ]);

        // Create the Subject
        /** @var Subject */
        $subject = Subject::first();

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

        CreateScoresTable::invoke($exam);

        $response = $this->get(route('exams.scores.upload', [
            'exam' => $exam,
            'subject' => $subject->id,
            'level-unit' => $levelUnit->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.scores.upload');

        $response->assertViewHasAll(['exam', 'subject', 'levelUnit', 'gradings', 'data', 'title']);
        
    }

    /** @group exam-scores */
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
