<?php

namespace Tests\Feature;

use App\Models\Grading;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradingsManagementTest extends TestCase
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

    /** @group gradings */
    public function testAUserCanVisitGradingsPage()
    {
        $this->withoutExceptionHandling();

        Grading::factory(2)->create();

        $response = $this->get(route('gradings.index'));

        $response->assertOk();

        $response->assertViewIs('gradings.index');

        $response->assertSeeLivewire('gradings');
        
    }
}
