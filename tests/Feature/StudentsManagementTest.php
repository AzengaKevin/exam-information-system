<?php

namespace Tests\Feature;

use App\Http\Livewire\AddStudentGuardians;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\Guardian;
use App\Http\Livewire\Students;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Stream;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
    }

    /** @group students */
    public function testAuthorizedUserCanBrowseStudents()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Browse']));

        Student::factory(2)->create();

        $response = $this->get(route('students.index'));

        $response->assertOk();

        $response->assertViewIs('students.index');

        $response->assertSeeLivewire('students');
        
    }

    /** @group students */
    public function testAuthorizedUserCanAddAStudent()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Create']));

        /** @var Level */
        $level = Level::factory()->create();

        /** @var Stream */
        $stream = Stream::factory()->create();

        LevelUnit::create([
            'level_id' => $level->id,
            'stream_id' => $stream->id,
            'alias' => "{$level->numeric}{$stream->alias}"
        ]);

        $payload = Student::factory([
            'admission_level_id' => $level->id,
            'stream_id' => $stream->id,
        ])->make()->toArray();

        Livewire::test(Students::class)
            ->set('adm_no', $payload['adm_no'])
            ->set('name', $payload['name'])
            ->set('gender', $payload['gender'])
            ->set('kcpe_marks', $payload['kcpe_marks'])
            ->set('kcpe_grade', $payload['kcpe_grade'])
            ->set('dob', $payload['dob'])
            ->set('admission_level_id', $payload['admission_level_id'])
            ->set('stream_id', $payload['stream_id'])
            ->set('hostel_id', $payload['hostel_id'])
            ->set('description', $payload['description'])
            ->call('addStudent');

        $student = Student::first();

        $this->assertNotNull($student);

        $this->assertEquals($payload['adm_no'], $student->adm_no);
        $this->assertEquals($payload['name'], $student->name);
        $this->assertEquals($payload['gender'], $student->gender);
        $this->assertEquals($payload['admission_level_id'], $student->admission_level_id);
        $this->assertEquals($payload['level_id'], $student->level_id);
        $this->assertEquals($payload['hostel_id'], $student->hostel_id);

        $this->assertNotNull($student->level_unit_id);
        
    }

    /** @group students */
    public function testAuthorizedUserCanAddStudentWithNameAndClassOnly()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Create']));
        
        /** @var LevelUnit */
        $LevelUnit = LevelUnit::factory()->create();

        $payload = Student::factory([
            'admission_level_id' => $LevelUnit->level->id,
            'stream_id' => $LevelUnit->stream->id,
            'adm_no' => null,
            'dob' => null,
            'kcpe_marks' => null,
            'kcpe_grade' => null,
            'gender' => null,
            'hostel_id' => null,
            'description' => null,
        ])->make()->toArray();

        Livewire::test(Students::class)
            ->set('name', $payload['name'])
            ->set('admission_level_id', $payload['admission_level_id'])
            ->set('stream_id', $payload['stream_id'])
            ->call('addStudent');

        /** @var Student */
        $student = Student::first();

        $this->assertNotNull($student);

        $this->assertEquals($payload['name'], $student->name);
        $this->assertEquals($payload['admission_level_id'], $student->admission_level_id);
        $this->assertEquals($payload['level_id'], $student->level_id);

        $this->assertNotNull($student->level_unit_id);

        $this->assertTrue($LevelUnit->is($student->levelUnit));
        
    }

    /** @group students */
    public function testAuthorizedUserCanUpdateAStudent()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Update']));

        /** @var Level */
        $level = Level::factory()->create();

        /** @var Stream */
        $stream = Stream::factory()->create();

        $LevelUnit = LevelUnit::create([
            'level_id' => $level->id,
            'stream_id' => $stream->id,
            'alias' => "{$level->numeric}{$stream->alias}"
        ]);        

        $student = Student::factory()->create();

        $payload = Student::factory([
            'level_id' => $level->id,
            'stream_id' => $stream->id
        ])->make()->toArray();

        Livewire::test(Students::class)
            ->call('editStudent', $student)
            ->set('adm_no', $payload['adm_no'])
            ->set('name', $payload['name'])
            ->set('gender', $payload['gender'])
            ->set('kcpe_marks', $payload['kcpe_marks'])
            ->set('kcpe_grade', $payload['kcpe_grade'])
            ->set('dob', $payload['dob'])
            ->set('level_id', $payload['level_id'])
            ->set('hostel_id', $payload['hostel_id'])
            ->set('stream_id', $payload['stream_id'])
            ->set('description', $payload['description'])
            ->call('updateStudent');

        $this->assertEquals($payload['adm_no'], $student->fresh()->adm_no);
        $this->assertEquals($payload['name'], $student->fresh()->name);
        $this->assertEquals($payload['gender'], $student->fresh()->gender);
        $this->assertEquals($payload['level_id'], $student->fresh()->level_id);
        $this->assertEquals($payload['hostel_id'], $student->fresh()->hostel_id);

        $this->assertEquals($LevelUnit->id, $student->fresh()->level_unit_id);
    }

    /** @group students */
    public function testAuthorizedUserCanDeleteAStudent()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Delete']));

        /** @var Student */
        $student = Student::factory()->create();

        Livewire::test(Students::class)
            ->call('showDeleteStudentModal', $student)
            ->call('deleteStudent');

        $this->assertFalse(Student::where('id', $student->id)->exists());
        
    }

    /** @group students */
    public function testAuthorizedUserCanAddStudentGuardians()
    {
        $this->withoutExceptionHandling();

        for ($i=0; $i < 2; $i++) { 
            
            /** @var Guardian */
            $guardian = Guardian::factory()->create();

            $guardian->auth()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->safeEmail(),
                'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                'password' => Hash::make('password')
            ]);
        }

        /** @var Student */
        $student = Student::factory()->create();

        $guardianIds = Guardian::all()->pluck('id');

        $payload = array();

        foreach ($guardianIds as $id) {
            $payload[$id] = 'true';
        }

        Livewire::test(AddStudentGuardians::class)
            ->call('showAddStudentGuardiansModal', $student)
            ->set('selectedGuardians', $payload)
            ->call('addStudentGuardians');

        $this->assertEquals(count($payload), $student->guardians()->count());
        
    }

    /** @group students */
    public function testAuthorizedUserCanVisitStudentsShowPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Read']));

        /** @var Student */
        $student = Student::factory()->create();
        
        $response = $this->get(route('students.show', $student));

        $response->assertOk();

        $response->assertViewIs('students.show');

        $response->assertViewHasAll(['student', 'systemSettings']);
        
    }
}
