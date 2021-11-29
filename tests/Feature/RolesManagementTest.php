<?php

namespace Tests\Feature;

use App\Http\Livewire\Roles;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class RolesManagementTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'Roles Create',
            'Roles Browse',
            'Roles Read',
            'Roles Update',
            'Roles Delete'
        ];

        array_walk($permissions, function($name){Permission::create(compact('name'));});

        /** @var Role */
        $role = Role::factory()->create();

        $role->permissions()->attach(Permission::all());

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $role->id
        ]);

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

    /** @group roles */
    public function testAuthorizedUserCanAssignPerssionsToRole()
    {
        $this->withoutExceptionHandling();

        /** @var Role */
        $role = Role::factory()->create();

        $permissions = Permission::factory(3)->create()->pluck('id')->toArray();

        $payload = array();
        
        foreach($permissions as $permission){
            $payload[$permission] = 'true';
        }

        Livewire::test(Roles::class)
            ->call('showUpdatePermissionsModal', $role)
            ->set('selectedPermissions', $payload)
            ->call('updatePermissions');

        $this->assertEquals(count($payload), $role->fresh()->permissions->count());
    }
}
