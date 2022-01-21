<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Str;
use App\Http\Livewire\Teachers;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class TeachersManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role = Role::factory()->create()]);

        $this->be($user);
    }

    /** @group teachers */
    public function testAuthorizedUserCanVisitTeachersPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Browse']));

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

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('teachers');
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanAddATeacher()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

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
    public function testAuthorizedUserCanAddATeacherWithoutAnEmailAddress()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

        Notification::fake();

        $payload = [
            'name' => $this->faker->name(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'employer' => $this->faker->randomElement(Teacher::employerOptions()),
            'tsc_number' => $this->faker->numberBetween(123456, 999999),
        ];

        Livewire::test(Teachers::class)
            ->set('name', $payload['name'])
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
        $this->assertEquals(Str::start($payload['phone'], '254'), $teacher->auth->phone);
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanAddATeacherWhilstAssigningSubjects()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

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

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Update']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Update']));

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

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Update']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Update']));

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

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Delete']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Delete']));

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
    }

    /** @group teachers */
    public function testAuthorizedTeacherCanViewTeacherDetailsPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Read']));

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

    /** @group teachers */
    public function testAuthorizedUserCanRestoreADeletedUser()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Restore']));

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $teacher->delete();

        $this->assertSoftDeleted($teacher);

        Livewire::test(Teachers::class)->call('restoreTeacher', $teacher->id);

        $this->assertFalse($teacher->fresh()->trashed());
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanDestroyATeacher()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Teachers Destroy']));

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Destroy']));

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $user = $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $teacher->delete();

        $this->assertSoftDeleted($teacher);

        Livewire::test(Teachers::class)->call('destroyTeacher', $teacher->id);
        
        $this->assertTrue(Teacher::where('id', $teacher->id)->withTrashed()->doesntExist());

        $this->assertTrue(User::where('id', $user->id)->withTrashed()->doesntExist());
        
    }
}
