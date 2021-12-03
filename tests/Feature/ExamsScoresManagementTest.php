<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use Livewire\Livewire;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\Hash;
use App\Http\Livewire\ExamQuickActions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;

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

    /** @group exam-score */
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

        $response->assertViewHasAll(['exam', 'subject', 'levelUnit']);
        
    }

    /** @group exam-score */
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
