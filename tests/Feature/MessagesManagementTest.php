<?php

namespace Tests\Feature;

use App\Http\Livewire\UserMessages;
use App\Models\Message;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class MessagesManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;

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
        $this->user = User::factory()->create([
            'role_id' => $role->id
        ]);

        $this->actingAs($this->user);
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

    /** @group messages */
    public function testAUserCanCreateAMessage()
    {
        $this->withoutExceptionHandling();

        Notification::fake();
        
        /** @var array */
        $payload = Message::factory()->make(['sender_id' => null])->toArray();

        Livewire::test(UserMessages::class, ['user' => $this->user])
            ->set('recipient_id', $payload['recipient_id'])
            ->set('content', $payload['content'])
            ->call('createMessage');
        
        $message = Message::first();

        $this->assertNotNull($message);

        $this->assertEquals(1, Message::for($this->user)->count());

    }
}
