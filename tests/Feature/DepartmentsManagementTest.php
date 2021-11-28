<?php

namespace Tests\Feature;

use App\Models\Department;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }


    /** @group departments */
    public function testAuthorizedUserCanVisitDEpartmentsPage()
    {
        $this->withoutExceptionHandling();

        Department::factory(2)->create();

        $response = $this->get(route('departments.index'));

        $response->assertOk();

        $response->assertViewIs('departments.index');

        $response->assertSeeLivewire('departments');
        
    }
}
