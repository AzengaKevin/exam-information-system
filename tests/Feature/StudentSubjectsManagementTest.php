<?php

namespace Tests\Feature;

use App\Http\Livewire\StudentSubjects;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentSubjectsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp() : void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
    }

    /** @group student-subjects */
    public function testAuthorizedUsersCanVisitStudentsSubjectsPage()
    {
        $this->withoutExceptionHandling();

        $this->artisan('db:seed --class=SubjectsSeeder');

        /** @var Student */
        $student = Student::factory()->create();

        $response = $this->get(route('students.subjects.index', $student));

        $response->assertOk();

        $response->assertViewIs('students.subjects.index');

        $response->assertViewHasAll(['student']);

        $response->assertSeeLivewire(StudentSubjects::class);
        
    }
}
