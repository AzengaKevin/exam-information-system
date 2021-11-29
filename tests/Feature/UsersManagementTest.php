<?php

namespace Tests\Feature;

use App\Http\Livewire\Users;
use App\Models\Permission;
use App\Models\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class UsersManagementTest extends TestCase
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

    /** @group users */
    public function testAuthorizedUserCanVisitUsersPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Browse']));

        User::factory(2)->create();

        $response = $this->get(route('users.index'));

        $response->assertOk();

        $response->assertViewIs('users.index');

        $response->assertSeeLivewire('users');
        
    }

    /** @group users */
    public function testAuthorizedUserCanUpdateUser()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Update']));

        /** @var User */
        $user = User::factory()->create();

        $payload = User::factory()->make()->toArray();

        Livewire::test(Users::class)
            ->call('editUser', $user)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->call('updateUser');

        $this->assertEquals($payload['name'], $user->fresh()->name);
        $this->assertEquals($payload['email'], $user->fresh()->email);
    }

    /** @group users */
    public function testAuthorizedUserCanUpdateUserWithRole()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Update']));

        /** @var User */
        $user = User::factory()->create();

        $payload = User::factory()->make()->toArray();

        /** @var Role */
        $role = Role::factory()->create();

        Livewire::test(Users::class)
            ->call('editUser', $user)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('role_id', $role->id)
            ->call('updateUser');

        $this->assertEquals($payload['name'], $user->fresh()->name);
        $this->assertEquals($payload['email'], $user->fresh()->email);
        $this->assertEquals($role->id, $user->fresh()->role_id);
        
    }

    /** @group users */
    public function testAuthorizedUserCanDeleteAUser()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Delete']));

        $user = User::factory()->create();

        Livewire::test(Users::class)
            ->call('showDeleteUserModal', $user)
            ->call('deleteUser');
        
        $this->assertFalse(User::where('id', $user->id)->exists());
    }

    /** @group users */
    public function testAuthorizedUserCanToggleUserActiveStatus()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $activeStatus = $user->active;

        Livewire::test(Users::class)->call('toggleUserActiveStatus', $user);

        $this->assertNotEquals($activeStatus, $user->fresh()->active);
        
    }
}
