<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MessagesManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $permissions = [
            'Messages Create',
            'Messages Browse',
            'Messages Read',
            'Messages Update',
            'Messages Delete'
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
    
    /** @group messages */
    public function testAuthorizedUserCanVisitMessagesPage()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('messages.index'));

        $response->assertOk();

        $response->assertViewIs('messages.index');

        $response->assertSeeLivewire('user-messages');
        
    }
}
