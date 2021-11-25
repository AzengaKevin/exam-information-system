<?php

namespace Tests\Feature;

use App\Http\Livewire\Guardians;
use App\Models\Guardian;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

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

    /** @group guardians */
    public function testAuthorizedUserAddAGuardian()
    {
        $this->withoutExceptionHandling();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        Livewire::test(Guardians::class)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('profession', $payload['profession'])
            ->set('location', $payload['location'])
            ->call('addGuardian');

        $guardian = Guardian::first();

        $this->assertNotNull($guardian);

        $this->assertEquals($payload['profession'], $guardian->profession);
        $this->assertEquals($payload['location'], $guardian->location);

        $this->assertNotNull($guardian->auth);

        $this->assertEquals($payload['name'], $guardian->auth->name);
        $this->assertEquals($payload['email'], $guardian->auth->email);

    }

    /** @group guardians */
    public function testAuthorizedUsersCanUpdateAGuardian()
    {
        $this->withoutExceptionHandling();

        /** @var Guardian */
        $guardian = Guardian::factory()->create();

        $guardian->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        Livewire::test(Guardians::class)
            ->call('editGuardian', $guardian)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('profession', $payload['profession'])
            ->set('location', $payload['location'])
            ->call('updateGuardian');

        $this->assertEquals($payload['profession'], $guardian->fresh()->profession);
        $this->assertEquals($payload['location'], $guardian->fresh()->location);

        $this->assertNotNull($guardian->fresh()->auth);

        $this->assertEquals($payload['name'], $guardian->fresh()->auth->name);
        $this->assertEquals($payload['email'], $guardian->fresh()->auth->email);
        
    }

    /** @group guardians */
    public function testAuthorizedUserCanDeleteAGuardian()
    {
        $this->withoutExceptionHandling();

        /** @var Guardian */
        $guardian = Guardian::factory()->create();

        $guardian->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);

        Livewire::test(Guardians::class)
            ->call('showDeleteGuardianModal', $guardian)
            ->call('deleteGuardian');

        $this->assertFalse(Guardian::where('id', $guardian->id)->exists());
        $this->assertFalse(User::where('id', $guardian->auth->id)->exists());
        
    }
}
