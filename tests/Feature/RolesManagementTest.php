<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Http\Livewire\Roles;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RolesManagementTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $this->role = Role::factory()->create()
        ]);

        $this->actingAs($user);
    }

    /** @group roles */
    public function testAuthorizedUserCanVisitRolesPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Browse']));

        Role::factory(2)->create();

        $response = $this->get(route('roles.index'));

        $response->assertOk();

        $response->assertViewIs('roles.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('roles');
        
    }

    /** @group roles */
    public function testAuthorizedUserCanCreateARole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Create']));

        $payload = Role::factory()->make()->toArray();

        Livewire::test(Roles::class)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->call('createRole');

        $role = Role::where('name', $payload['name'])->first();

        $this->assertNotNull($role);

        $this->assertEquals($payload['name'], $role->name);
        $this->assertEquals(Str::slug($payload['name']), $role->slug);
        $this->assertEquals($payload['description'], $role->description);

    }

    /** @group roles */
    public function testAuthotrizedUserCanUpdateARole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Update']));

        /** @var Role */
        $role = Role::factory()->create();

        $payload = Role::factory()->make()->toArray();

        Livewire::test(Roles::class)
            ->call('editRole', $role)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->call('updateRole');

        $this->assertEquals($payload['name'], $role->fresh()->name);
        $this->assertEquals(Str::slug($payload['name']), $role->fresh()->slug);
        $this->assertEquals($payload['description'], $role->fresh()->description);
        
    }

    /** @group roles */
    public function testAuthorizedUserCanDeleteARole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Delete']));

        /** @var Role */
        $role = Role::factory()->create();

        Livewire::test(Roles::class)
            ->call('showDeleteRoleModal', $role)
            ->call('deleteRole');

        $this->assertFalse(Role::where('id', $role->id)->exists());
        
    }

    /** @group roles */
    public function testAuthorizedUserCanAssignPerssionsToRole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Manage Permissions']));

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

    /** @group roles */
    public function testAuthorizedCanRestoreARole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Restore']));

        /** @var Role */
        $role = Role::factory()->create();

        $role->delete();

        $this->assertSoftDeleted($role);

        Livewire::test(Roles::class)
            ->call('restoreRole', $role->id);

        $this->assertFalse($role->fresh()->trashed());
        
    }

    /** @group roles */
    public function testAuthorizedUserCanDestroyARole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Roles Destroy']));

        /** @var Role */
        $role = Role::factory()->create();

        $role->delete();

        $this->assertSoftDeleted($role);

        Livewire::test(Roles::class)
            ->call('destroyRole', $role->id);

        $this->assertFalse(Role::where('id', $role->id)->withTrashed()->exists());
        
    }
}
