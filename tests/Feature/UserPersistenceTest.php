<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group users */
    public function testAUserCanBePersistedToTheDatabase()
    {
        $payload = array(
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        );

        $user = User::create($payload);

        $this->assertNotNull($user);

        $this->assertEquals($payload['name'], $user->name);
        $this->assertEquals($payload['email'], $user->email);
        
    }
}
