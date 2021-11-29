<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionsManagmentTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $this->role->id
        ]);

        $this->actingAs($user);
    }

    /** @group permissions */
    public function testAuthorizedUserCanBrowsePermissions()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Permissions Browse']));

        Permission::factory(2)->create();

        $response = $this->get(route('permissions.index'));

        $response->assertOk();

        $response->assertViewIs('permissions.index');

        $response->assertSeeLivewire('permissions');
        
    }

}
