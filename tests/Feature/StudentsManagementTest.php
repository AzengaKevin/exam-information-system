<?php

namespace Tests\Feature;

use App\Http\Livewire\Students;
use App\Models\Student;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class StudentsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group students */
    public function testAuthorizedUserCanBrowseStudents()
    {
        $this->withoutExceptionHandling();

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

        $payload = Student::factory()->make()->toArray();

        Livewire::test(Students::class)
            ->set('adm_no', $payload['adm_no'])
            ->set('name', $payload['name'])
            ->set('gender', $payload['gender'])
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
        
    }

    /** @group students */
    public function testAuthorizedUserCanUpdateAStudent()
    {
        $this->withoutExceptionHandling();

        $student = Student::factory()->create();

        $payload = Student::factory()->make()->toArray();

        Livewire::test(Students::class)
            ->call('editStudent', $student)
            ->set('adm_no', $payload['adm_no'])
            ->set('name', $payload['name'])
            ->set('gender', $payload['gender'])
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
}
