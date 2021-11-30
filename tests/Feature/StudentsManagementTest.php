<?php

namespace Tests\Feature;

use App\Http\Livewire\AddStudentGuardians;
use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\Guardian;
use App\Http\Livewire\Students;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var Role */
    private $role;

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

        $payload = Student::factory()->make()->toArray();

        Livewire::test(Students::class)
            ->set('adm_no', $payload['adm_no'])
            ->set('name', $payload['name'])
            ->set('gender', $payload['gender'])
            ->set('kcpe_marks', $payload['kcpe_marks'])
            ->set('kcpe_grade', $payload['kcpe_grade'])
            ->set('dob', $payload['dob'])
            ->set('admission_level_id', $payload['admission_level_id'])
            ->set('stream_id', $payload['stream_id'])
            ->set('description', $payload['description'])
            ->call('addStudent');

        $student = Student::first();

        $this->assertNotNull($student);

        $this->assertEquals($payload['adm_no'], $student->adm_no);
        $this->assertEquals($payload['name'], $student->name);
        $this->assertEquals($payload['gender'], $student->gender);
        $this->assertEquals($payload['admission_level_id'], $student->admission_level_id);
        $this->assertEquals($payload['level_id'], $student->level_id);

        $this->assertNotNull($student->level_unit_id);
        
    }

    /** @group students */
    public function testAuthorizedUserCanUpdateAStudent()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Update']));

        $student = Student::factory()->create();

        $payload = Student::factory()->make()->toArray();

        Livewire::test(Students::class)
            ->call('editStudent', $student)
            ->set('adm_no', $payload['adm_no'])
            ->set('name', $payload['name'])
            ->set('gender', $payload['gender'])
            ->set('kcpe_marks', $payload['kcpe_marks'])
            ->set('kcpe_grade', $payload['kcpe_grade'])
            ->set('dob', $payload['dob'])
            ->set('admission_level_id', $payload['admission_level_id'])
            ->set('stream_id', $payload['stream_id'])
            ->set('description', $payload['description'])
            ->call('updateStudent');

        $this->assertEquals($payload['adm_no'], $student->fresh()->adm_no);
        $this->assertEquals($payload['name'], $student->fresh()->name);
        $this->assertEquals($payload['gender'], $student->fresh()->gender);
        $this->assertEquals($payload['admission_level_id'], $student->fresh()->admission_level_id);
        $this->assertEquals($payload['level_id'], $student->fresh()->level_id);
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
}
