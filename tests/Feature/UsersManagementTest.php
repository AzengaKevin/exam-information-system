<?php

namespace Tests\Feature;

use App\Http\Livewire\Users;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class UsersManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();
        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group users */
    public function testAuthorizedUserCanVisitUsersPage()
    {
        $this->withoutExceptionHandling();

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

        /** @var User */
        $user = User::factory()->create();

        $payload = User::factory()->make()->toArray();

        Livewire::test(Users::class)
            ->call('editUser', $user)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->call('updateUser');

        $this->assertNotNull($user);

        $this->assertEquals($payload['name'], $user->fresh()->name);
        $this->assertEquals($payload['email'], $user->fresh()->email);
    }

    /** @group users */
    public function testAuthorizedUserCanDeleteAUser()
    {
        $this->withoutExceptionHandling();

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
