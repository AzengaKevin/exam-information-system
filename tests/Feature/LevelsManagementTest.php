<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Permission;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelsManagementTest extends TestCase
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
    public function testAuthorizedUserCanBrowseLevels()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Browse']));

        Level::factory(2)->create();

        $response = $this->get(route('levels.index'));

        $response->assertOk();

        $response->assertViewIs('levels.index');

        $response->assertSeeLivewire('levels');
        
    }    
}
