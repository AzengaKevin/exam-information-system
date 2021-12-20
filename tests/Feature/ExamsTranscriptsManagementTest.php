<?php

namespace Tests\Feature;

use App\Actions\Exam\CreateScoresTable;
use Tests\TestCase;
use App\Models\Exam;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Role;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

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

        $response->assertViewHasAll(['students', 'exam', 'level', 'levelUnit']);
        
    }

    /** @group exams-transcripts */
    public function testAuthorizedUserCanSeeStudentProvisionalTranscript()
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
