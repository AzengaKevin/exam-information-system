<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Level;
use Livewire\Livewire;
use App\Models\Grading;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Actions\Exam\CreateScoresTable;
use App\Http\Livewire\SubjectExamScores;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectExamScoresTest extends TestCase
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


    /** @group exam-scores */
    public function testAuthorizedUserCanRankLevelSubjectScores()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');
        $this->artisan('db:seed --class=GradingSeeder');
    
        /** @var Level */
        $level = Level::factory()->create();
        
        $students = Student::factory(2)->create([
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

        // Upload some scores for the subject
        $grading = Grading::find($data['grading_id'] ?? 1) ?? Grading::first();

        $values = $grading->values;

        // Process Uploading the scores
        $scores = array();

        foreach ($students as $student) {
            $scores[$student->id] = [
                'score' => $this->faker->numberBetween(0, 100),
                'extra' => null
            ];
        }

        foreach ($scores as $studentId => $scoreData) {

            $score = $scoreData['score'] ?? null;
            $grade = null;
            $points = null;

            $extra = $scoreData['extra'] ?? null;

            if ($score) {
                foreach ($values as $value) {
                    if($score >= $value['min'] && $score <= $value['max']){
                        $grade = $value['grade'];
                        $points = $value['points'];
                        break;
                    }
                }
            }

            if ($extra) {
                $score = 0;
                switch ($extra) {
                    case 'missing':
                        $points = 'X';
                        break;
                    case 'cheated':
                        $points = 'Y';
                        break;
                    default:
                        $points = 'P';
                        break;
                }
            }

            DB::table(Str::slug($exam->shortname))
                ->updateOrInsert([
                    "student_id" => $studentId
                ], [
                    $subject->shortname => json_encode([
                        'score' => $score,
                        'grade' => $grade,
                        'points' => $points,
                    ]),
                    'level_id' => optional($level)->id,
                ]);
        }
        
        Livewire::test(SubjectExamScores::class, [
            'exam' => $exam,
            'subject' => $subject,
            'level' => $level,
            'levelUnit' => null
        ])->call('rankSubjectResults');
        
    }
    
    /** @group exam-scores */
    public function testAuthorizedUserCanGenerateTotalScoreForSegmentSubjectts()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=GradingSeeder');
    
        /** @var Level */
        $level = Level::factory()->create();

        $students = Student::factory(2)->create([
            'kcpe_marks' => null,
            'kcpe_grade' => null,
            'stream_id' => null,
            'admission_level_id' => $level->id,
        ]);

        /** @var Subject */
        $subject = Subject::factory()->create([
            'name' => 'English',
            'shortname' => 'eng',
            'segments' => [ 'outOf60' => 60, 'comp' => 40]
        ]);

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

        $exam->subjects()->attach($subject);

        // Create Subject Scores
        $scores = array();

        foreach ($students as $student) {
            $scores[$student->id]['outOf60'] = $this->faker->numberBetween(0, 60);
            $scores[$student->id]['comp'] = $this->faker->numberBetween(0, 40);
        }

        CreateScoresTable::invoke($exam);

        $tblName = Str::slug($exam->shortname);

        // Process Uploading the scores
        foreach ($scores as $stid => $scoreData) {

            DB::table($tblName)
                ->updateOrInsert(["student_id" => $stid], [
                    $subject->shortname => json_encode(array_merge($scoreData, [
                        'score' => null,
                        'grade' => null,
                        'points' => null
                    ])),
                    'level_id' => optional($level)->id
                ]);
        }
        
        Livewire::test(SubjectExamScores::class, [
            'exam' => $exam,
            'subject' => $subject,
            'level' => $level,
            'levelUnit' => null
        ])->call('calculateTotalScore'); 
        
    }
}
