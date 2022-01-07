<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Http\Livewire\Teachers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class TeachersManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->be($user);
    }

    /** @group teachers */
    public function testAuthorizedUserCanVisitTeachersPage()
    {
        $this->withoutExceptionHandling();

        for ($i=0; $i < 2; $i++) {
            
            /** @var Teacher */
            $teacher = Teacher::factory()->create();

            $teacher->auth()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->safeEmail(),
                'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                'password' => Hash::make('password')
            ]);
        }

        $response = $this->get(route('teachers.index'));

        $response->assertOk();

        $response->assertViewIs('teachers.index');

        $response->assertSeeLivewire('teachers');
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanAddATeacher()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'employer' => $this->faker->randomElement(Teacher::employerOptions()),
            'tsc_number' => $this->faker->numberBetween(123456, 999999),
        ];

        Livewire::test(Teachers::class)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
            ->set('employer', $payload['employer'])
            ->set('tsc_number', $payload['tsc_number'])
            ->call('addTeacher');

        $teacher = Teacher::first();

        $this->assertNotNull($teacher);

        $this->assertEquals($payload['employer'], $teacher->employer);
        $this->assertEquals($payload['tsc_number'], $teacher->tsc_number);

        $this->assertNotNull($teacher->auth);

        $this->assertEquals($payload['name'], $teacher->auth->name);
        $this->assertEquals($payload['email'], $teacher->auth->email);
        $this->assertEquals(Str::start($payload['phone'], '254'), $teacher->auth->phone);
    }

    /** @group teachers */
    public function testAuthorizedUserCanAddATeacherWhilstAssigningSubjects()
    {
        $this->withoutExceptionHandling();

        $subjectsIds = Subject::factory()->create()->pluck('id')->toArray();

        $selectedSubjects = array();

        foreach ($subjectsIds as $id) {
            $selectedSubjects[$id] = true;
        }

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'employer' => $this->faker->randomElement(Teacher::employerOptions()),
            'tsc_number' => $this->faker->numberBetween(123456, 999999),
            'selectedSubjects' => $selectedSubjects
        ];

        Livewire::test(Teachers::class)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
            ->set('employer', $payload['employer'])
            ->set('tsc_number', $payload['tsc_number'])
            ->set('selectedSubjects', $payload['selectedSubjects'])
            ->call('addTeacher');

        /** @var Teacher */
        $teacher = Teacher::first();

        $this->assertNotNull($teacher);

        $this->assertEquals($payload['employer'], $teacher->employer);
        $this->assertEquals($payload['tsc_number'], $teacher->tsc_number);

        $this->assertNotNull($teacher->auth);

        $this->assertEquals($payload['name'], $teacher->auth->name);
        $this->assertEquals($payload['email'], $teacher->auth->email);
        $this->assertEquals(Str::start($payload['phone'], '254'), $teacher->auth->phone);

        $this->assertEquals(count($selectedSubjects), $teacher->subjects()->count());
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanUpdateATeacher()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'employer' => $this->faker->randomElement(Teacher::employerOptions()),
            'tsc_number' => $this->faker->numberBetween(123456, 999999),
        ];

        Livewire::test(Teachers::class)
            ->call('editTeacher', $teacher)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
            ->set('employer', $payload['employer'])
            ->set('tsc_number', $payload['tsc_number'])
            ->call('updateTeacher');

        $this->assertEquals($payload['employer'], $teacher->fresh()->employer);
        $this->assertEquals($payload['tsc_number'], $teacher->fresh()->tsc_number);

        $this->assertNotNull($teacher->fresh()->auth);

        $this->assertEquals($payload['name'], $teacher->fresh()->auth->name);
        $this->assertEquals($payload['email'], $teacher->fresh()->auth->email);
        $this->assertEquals(Str::start($payload['phone'], '254'), $teacher->fresh()->auth->phone);
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanUpdateATeacherWhilstUpdatingTeacherSubjects()
    {        
        
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $subjectsIds = Subject::factory()->create()->pluck('id')->toArray();

        $selectedSubjects = array();

        foreach ($subjectsIds as $id) {
            $selectedSubjects[$id] = true;
        }

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'employer' => $this->faker->randomElement(Teacher::employerOptions()),
            'tsc_number' => $this->faker->numberBetween(123456, 999999),
            'selectedSubjects' => $selectedSubjects
        ];

        Livewire::test(Teachers::class)
            ->call('editTeacher', $teacher)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
            ->set('employer', $payload['employer'])
            ->set('tsc_number', $payload['tsc_number'])
            ->set('selectedSubjects', $payload['selectedSubjects'])
            ->call('updateTeacher');

        $this->assertEquals($payload['employer'], $teacher->fresh()->employer);
        $this->assertEquals($payload['tsc_number'], $teacher->fresh()->tsc_number);

        $this->assertNotNull($teacher->fresh()->auth);

        $this->assertEquals($payload['name'], $teacher->fresh()->auth->name);
        $this->assertEquals($payload['email'], $teacher->fresh()->auth->email);
        $this->assertEquals(Str::start($payload['phone'], '254'), $teacher->fresh()->auth->phone);

        $this->assertEquals(count($selectedSubjects), $teacher->fresh()->subjects()->count());
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanDeleteATeacher()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        Livewire::test(Teachers::class)
            ->call('showDeleteTeacherModal', $teacher)
            ->call('deleteTeacher');
        
        $this->assertFalse(Teacher::where('id', $teacher->id)->exists());
        $this->assertFalse(User::where('id', $teacher->auth->id)->exists());
    }

    /** @group teachers */
    public function testAuthorizedTeacherCanViewTeacherDetailsPage()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $response = $this->get(route('teachers.show', $teacher));

        $response->assertOk();

        $response->assertViewIs('teachers.show');

        $response->assertViewHasAll(['teacher']);

        $response->assertSeeLivewire('teacher-responsibilities');
        $response->assertSeeLivewire('teacher-subjects');
        
    }
}
