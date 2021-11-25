<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesManagementTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group roles */
    public function testAuthorizedUserCanVisitRolesPage()
    {
        $this->withoutExceptionHandling();

        Role::factory(2)->create();

        $response = $this->get(route('roles.index'));

        $response->assertOk();

        $response->assertViewIs('roles.index');

        $response->assertSeeLivewire('roles');
        
    }

    /** group roles */
    public function testAuthorizedUserCanAssignPerssionsToRole()
    {
        $this->withoutExceptionHandling();

        
        
    }
}
