<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Livewire\TeacherResponsibilities;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

class TeachersResponsibilitiesManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group teachers-responsibilities */
    public function testAuthorizedUserCanVisitTeacherResponsibilityManagementPage()
    {
        $this->withoutExceptionHandling();
            
        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);

        $responsibilities = Responsibility::factory(2)->create()->pluck('id')->toArray();

        $teacher->responsibilities()->sync($responsibilities);

        $response = $this->get(route('teachers.responsibilities.index', $teacher));

        $response->assertOk();

        $response->assertViewIs('teachers.responsibilities.index');

        $response->assertViewHasAll(['teacher']);

        $response->assertSeeLivewire('teacher-responsibilities');
        
    }

    /** @group teachers-responsibilities */
    public function testAuthorizedUserCanAssignATeacherResponsibility()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);
        
        $responsibility = Responsibility::factory()->create([
            'requirements' => ['class']
        ]);

        $levelUnit = LevelUnit::factory()->create();

        Livewire::test(TeacherResponsibilities::class, ['teacher' => $teacher])
            ->set('responsibility_id', $responsibility->id)
            ->set('level_unit_id', $levelUnit->id)
            ->call('assignResponsibility');

        $this->assertEquals(1, $teacher->responsibilities()->count());

        $this->assertNotNull($teacher->fresh()->responsibilities->first()->pivot->levelUnit);
    }

    /** @group teachers-responsibilities */
    public function testAuthorizedUserCanRevokeTeacherResponsibilities()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);
        
        $responsibility = Responsibility::factory()->create();

        $teacher->responsibilities()->attach($responsibility);

        $this->assertEquals(1, $teacher->fresh()->responsibilities()->count());

        $id = $teacher->responsibilities()->first()->pivot->id;

        Livewire::test(TeacherResponsibilities::class, ['teacher' => $teacher])
            ->call('removeResponsibility', $id);

        $this->assertEquals(0, $teacher->fresh()->responsibilities()->count());
        
    }

    /** @group teacher-responsibilities */
    public function testAuthorizedUserCanAssignBulkSubjectResponsibilitiiesToTeacher()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');

        /** @var Teacher */
        $teacher1 = Teacher::factory()->create();

        /** @var Teacher */
        $teacher2 = Teacher::factory()->create();

        // Teacher 1 Subjects
        $t1Subjects = Subject::whereIn('id', [1,2])->get();
        $teacher1->subjects()->attach($t1Subjects);

        // Teacher 2 Subjects
        $t2Subjects = Subject::whereIn('id', [1,3])->get();
        $teacher2->subjects()->attach($t2Subjects);

        // Create 5 Classes
        LevelUnit::factory(5)->create();

        // Assign the responsibility for the first two classes to teacher 1
        /** @var Collection */
        $t1S1Classes = LevelUnit::whereIn('id', [1,2])->get();

        $subject1 = Subject::findOrFail(1);

        /** @var Responsibility */
        $responsibility = Responsibility::factory()->create([
            'name' => 'Subject Teacher',
            'requirements' => ['class', 'subject']
        ]);
        
        $t1S1Classes->each(function($classItem) use ($subject1, $teacher1, $responsibility){
            $teacher1->responsibilities()->attach($responsibility,[
                'level_unit_id' => $classItem->id,
                'subject_id' => $subject1->id
            ]);
        });
        
        Livewire::test(TeacherResponsibilities::class, ['teacher' => $teacher2])
            ->set('teacher_subject_id', $subject1->id)
            ->set('selectAllClasses', 'true')
            ->call('assignBulkResponsibilities');

        $this->assertEquals(3, $teacher2->fresh()->responsibilities()->count());

        $leveUnitIds = DB::table('responsibility_teacher')->select(['level_unit_id'])->where([
            ['teacher_id', $teacher2->id],
            ['subject_id', $subject1->id]
        ])->get()->pluck('level_unit_id')->toArray();

        $this->assertEquals([3, 4, 5], $leveUnitIds);
        
    }
}
