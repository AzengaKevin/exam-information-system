<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use Illuminate\Support\Facades\Hash;
use App\Actions\Exam\CreateScoresTable;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamsAnalysisManagementTest extends TestCase
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

    /** @group exam-analysis */
    public function testAuthorizedUserCanVisitExamAnalysisPage()
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

        $response = $this->get(route('exams.analysis.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.analysis.index');

        $response->assertViewHasAll(['exam']);
        
    }

    /** @group exam-analysis */
    public function testAuthorizedUserCanVisitExamAnalysisLevelPage()
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

        $response = $this->get(route('exams.analysis.index', [
            'exam' => $exam,
            'level' => $levelUnit->level
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.analysis.index');

        $response->assertViewHasAll(['exam', 'level']);
        
    }
}
