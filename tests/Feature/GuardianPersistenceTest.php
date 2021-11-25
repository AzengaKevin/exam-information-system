<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Guardian;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GuardianPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group guardian */
    public function testAGuardianCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Guardian::factory()->make()->toArray();

        /** @var Guardian */
        $guardian = Guardian::create($payload);

        $guardian->auth()->create([
            'name' => $name = $this->faker->name(),
            'email' => $email = $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);

        $this->assertEquals($payload['profession'], $guardian->profession);
        $this->assertEquals($payload['location'], $guardian->location);

        $this->assertNotNull($guardian->fresh()->auth);

        $this->assertEquals($name, $guardian->fresh()->auth->name);
        $this->assertEquals($email, $guardian->fresh()->auth->email);
        
    }
}
