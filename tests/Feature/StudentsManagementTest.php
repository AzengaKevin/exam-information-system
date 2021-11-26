<?php

namespace Tests\Feature;

use App\Models\Student;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
