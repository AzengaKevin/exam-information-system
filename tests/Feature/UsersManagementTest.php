<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
}
