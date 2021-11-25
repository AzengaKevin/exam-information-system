<?php

namespace Tests\Feature;

use App\Models\Guardian;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class GuardiansManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group guardians */
    public function testAuthorizedUserCanBrowseGuardians()
    {
        $this->withoutExceptionHandling();

        for ($i=0; $i < 2; $i++) { 
            
            /** @var Guardian */
            $guardian = Guardian::factory()->create();

            $guardian->auth()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->safeEmail(),
                'password' => Hash::make('password')
            ]);
        }        

        $response = $this->get(route('guardians.index'));

        $response->assertOk();

        $response->assertViewIs('guardians.index');

        $response->assertSeeLivewire('guardians');
    }
}
