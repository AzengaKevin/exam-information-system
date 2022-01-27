<?php

namespace Tests\Feature;

use App\Http\Livewire\Permissions;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('permissions');
        
    }

    /** @group permissions */
    public function testAuthorizedUserCanCreateAPermission()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Permissions Create']));

        /** @var array */
        $payload = Permission::factory()->make()->toArray();

        Livewire::test(Permissions::class)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->call('createPermission');

        $permission = Permission::where('name', $payload['name'])->first();

        $this->assertNotNull($permission);

        $this->assertEquals($payload['name'], $permission->name);
        $this->assertEquals($payload['description'], $permission->description);
            
    }

    /** @group permissions */
    public function testAuthorizedUserCanUpdateAPermission()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Permissions Update']));

        /** @var Permission */
        $permission = Permission::factory()->create();

        /** @var array */
        $payload = Permission::factory()->make()->toArray();

        Livewire::test(Permissions::class)
            ->call('editPermission', $permission)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->call('updatePermission');

        $this->assertEquals($payload['name'], $permission->fresh()->name);
        $this->assertEquals($payload['description'], $permission->fresh()->description);

    }

    /** @group permissions */
    public function testAuthorizedUserCanUpdateAPermissionsIncludingTheLockedProperty()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Permissions Update']));

        /** @var Permission */
        $permission = Permission::factory()->create();

        /** @var array */
        $payload = Permission::factory()->make(['locked' => false])->toArray();

        Livewire::test(Permissions::class)
            ->call('editPermission', $permission)
            ->set('name', $payload['name'])
            ->set('locked', $payload['locked'])
            ->set('description', $payload['description'])
            ->call('updatePermission');

        $this->assertEquals($payload['name'], $permission->fresh()->name);
        $this->assertEquals($payload['locked'], $permission->fresh()->locked);
        $this->assertEquals($payload['description'], $permission->fresh()->description);
        
    }

    /** @group permissions */
    public function testAuthorizedUserCanDeleteAPermission()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Permissions Delete']));

        /** @var Permission */
        $permission = Permission::factory()->create();

        Livewire::test(Permissions::class)
            ->call('showDeletePermissionModal', $permission)
            ->call('deletePermission');
        
        $this->assertSoftDeleted($permission);
        
    }

    /** @group permissions */
    public function testAuthorizedUserCanToggledLockedPermissionProperty()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Permissions Update Locked']));

        /** @var Permission */
        $permission = Permission::factory()->create();

        Livewire::test(Permissions::class)->call('togglePermissionLockedStatus', $permission);

        $this->assertFalse($permission->fresh()->locked);
        
    }

    /** @group permissions */
    public function testAuthorizedUserCanRestoreATrashedPermission()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Permissions Restore']));

        /** @var Permission */
        $permission = Permission::factory()->create();

        $permission->delete();

        $this->assertSoftDeleted($permission);

        Livewire::test(Permissions::class)
            ->call('restorePermission', $permission->id);
        
        $this->assertFalse($permission->fresh()->trashed());
        
    }

    /** @group permissions */
    public function testAuthorizedUserCanDestroyATrashedPermission()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Permissions Destroy']));

        /** @var Permission */
        $permission = Permission::factory()->create();

        $permission->delete();

        Livewire::test(Permissions::class)
            ->call('destroyPermission', $permission->id);
        
        $this->assertTrue(Permission::where('id', $permission->id)->withTrashed()->doesntExist());
        
    }

}
