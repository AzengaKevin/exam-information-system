<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Teacher;
use App\Models\Permission;
use App\Http\Livewire\Users;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

        $response->assertViewHasAll(['trashed', 'roleId']);

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

        /** @var User */
        $user = User::factory()->create();

        Livewire::test(Users::class)
            ->call('showDeleteUserModal', $user)
            ->call('deleteUser');
            
        $this->assertSoftDeleted($user);
    }

    /** @group users */
    public function testAuthorizedUserCanToggleUserActiveStatus()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Update']));

        $user = User::factory()->create();

        $activeStatus = $user->active;

        Livewire::test(Users::class)->call('toggleUserActiveStatus', $user);

        $this->assertNotEquals($activeStatus, $user->fresh()->active);
        
    }

    /** @group users */
    public function testAuthorizedUserCanPerformBulkUserRoleUpdate()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Bulk Update']));

        $usersIds = User::factory(5)->create()->pluck('id')->toArray();

        $selectedUsers = array();

        foreach ($usersIds as $id) {
            $selectedUsers[$id] = 'true';
        }

        /** @var Role */
        $role = Role::factory()->create();

        Livewire::test(Users::class)
            ->set('role_id', $role->id)
            ->set('selectedUsers', $selectedUsers)
            ->call('bulkUsersRoleUpdate');

        $this->assertEquals($usersIds, $role->users()->get()->pluck('id')->toArray());
        
    }

    /** @group users */
    public function testAuthorizeUserCanRestoreTrashedTeachers()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Restore']));
            
        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $user = $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $user->delete();

        $this->assertSoftDeleted($user);

        Livewire::test(Users::class)
            ->call('restoreUser', $user->id);
    
        $this->assertFalse($user->fresh()->trashed());
        
    }


    /** @group users */
    public function testAuthorizeUserCanDestroyTrashedTeachers()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::create(['name' => 'Users Destroy']));
            
        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $user = $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $user->delete();

        $this->assertSoftDeleted($user);

        Livewire::test(Users::class)
            ->call('destroyUser', $user->id);
        
        $this->assertTrue(User::where('id', $user->id)->withTrashed()->doesntExist());
        
    }    
}
