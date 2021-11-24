<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomeTest extends TestCase
{

    use RefreshDatabase, WithFaker;
    
    /** @group home */
    public function testAuthenticatedUserCanVisitHome()
    {
        /** @var Authenticatable */
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }
}
