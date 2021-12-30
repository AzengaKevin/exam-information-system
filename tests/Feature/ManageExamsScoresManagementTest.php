<?php

namespace Tests\Feature;

use App\Actions\Exam\CreateScoresTable;
use App\Models\Exam;
use App\Models\Level;
use App\Models\Responsibility;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageExamsScoresManagementTest extends TestCase
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


    /** @group exams-scores */
    public function testAuthorizedUserCanVisitManageLevelSubjectScores()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');
        $this->artisan('db:seed --class=GradingSeeder');
    
        /** @var Level */
        $level = Level::factory()->create();
        
        Student::factory(2)->create([
            'kcpe_marks' => null,
            'kcpe_grade' => null,
            'stream_id' => null,
            'admission_level_id' => $level->id,
        ]);

        /** @var Subject */
        $subject = Subject::first();

        // Create Responsibility for the current teacher
        $responsibility = Responsibility::firstOrCreate(['name' => 'Subject Teacher']);

        // Associate Teacher and Responsibility
        $this->teacher->responsibilities()->attach($responsibility, [
            'level_id' => $level->id,
            'subject_id' => $subject->id
        ]);

        /** @var Exam */
        $exam = Exam::factory()->create();

        $exam->levels()->attach($level);

        $subjects = Subject::limit(2)->get();

        $exam->subjects()->attach($subjects);

        CreateScoresTable::invoke($exam);

        $response = $this->get(route('exams.scores.manage', [
            'exam' => $exam,
            'subject' => $subject->id,
            'level' => $level->id
        ]));

        $response->assertOk();

        $response->assertViewIs('exams.scores.manage');

        $response->assertViewHasAll(['exam', 'subject', 'level', 'title']);

        $response->assertSeeLivewire('subject-exam-scores');
        
    }    

    
}
