<?php

namespace Tests\Feature;

use App\Http\Livewire\Guardians;
use App\Models\Guardian;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class GuardiansManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $this->role = Role::factory()->create()
        ]);

        $this->actingAs($user);
    }

    /** @group guardians */
    public function testAuthorizedUserCanBrowseGuardians()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Browse']));

        for ($i=0; $i < 2; $i++) { 
            
            /** @var Guardian */
            $guardian = Guardian::factory()->create();

            $guardian->auth()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->safeEmail(),
                'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                'password' => Hash::make('password')
            ]);
        }        

        $response = $this->get(route('guardians.index'));

        $response->assertOk();

        $response->assertViewIs('guardians.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('guardians');
    }

    /** @group guardians */
    public function testAuthorizedUserAddAGuardian()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        Livewire::test(Guardians::class)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
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
    public function testAuthorizedUserAddAGuardianWithoutEmailAddress()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

        $payload = [
            'name' => $this->faker->name(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        Livewire::test(Guardians::class)
            ->set('name', $payload['name'])
            ->set('phone', $payload['phone'])
            ->set('profession', $payload['profession'])
            ->set('location', $payload['location'])
            ->call('addGuardian');

        $guardian = Guardian::first();

        $this->assertNotNull($guardian);

        $this->assertEquals($payload['profession'], $guardian->profession);
        $this->assertEquals($payload['location'], $guardian->location);

        $this->assertNotNull($guardian->auth);

        $this->assertEquals($payload['name'], $guardian->auth->name);
        
    }

    /** @group guardians */
    public function testAuthorizedUsersCanUpdateAGuardian()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Update']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Update']));

        /** @var Guardian */
        $guardian = Guardian::factory()->create();

        $guardian->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        Livewire::test(Guardians::class)
            ->call('editGuardian', $guardian)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('phone', $payload['phone'])
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

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Delete']));

        /** @var Guardian */
        $guardian = Guardian::factory()->create();

        $guardian->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        Livewire::test(Guardians::class)
            ->call('showDeleteGuardianModal', $guardian)
            ->call('deleteGuardian');

        $this->assertSoftDeleted($guardian);
        
    }

    /** @group guardians */
    public function testAnAuthorizedUserCanRestoreATrashedGuardian()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Restore']));

        /** @var Guardian */
        $guardian = Guardian::factory()->create();

        $guardian->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $guardian->delete();

        $this->assertSoftDeleted($guardian);

        Livewire::test(Guardians::class)->call('restoreGuardian', $guardian->id);

        $this->assertFalse($guardian->fresh()->trashed());
        
    }

    /** @group gaurdians */
    public function testAuthorizedUserCanPermanentlyDeleteAGuardianFromTheSysten()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Destroy']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Destroy']));

        /** @var Guardian */
        $guardian = Guardian::factory()->create();

        /** @var User */
        $user = $guardian->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $guardian->delete();

        Livewire::test(Guardians::class)->call('destroyGuardian', $guardian->id);

        $this->assertTrue(Guardian::where('id', $guardian->id)->withTrashed()->doesntExist());

        $this->assertTrue(User::where('id', $user->id)->withTrashed()->doesntExist());
    }
}
