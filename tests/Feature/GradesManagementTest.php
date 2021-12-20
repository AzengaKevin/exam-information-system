<?php

namespace Tests\Feature;

use App\Models\Grade;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradesManagementTest extends TestCase
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

    /** @group gradings */
    public function testAUserCanVisitGradesPage()
    {
        $this->withoutExceptionHandling();

        Grade::factory(2)->create();

        $response = $this->get(route('grades.index'));

        $response->assertOk();

        $response->assertViewIs('grades.index');

        $response->assertSeeLivewire('grades');
        
    }
}
