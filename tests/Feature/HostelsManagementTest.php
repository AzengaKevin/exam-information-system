<?php

namespace Tests\Feature;

use App\Http\Livewire\Hostels;
use App\Models\Hostel;
use App\Models\Permission;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class HostelsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $this->role = Role::factory()->create()
        ]);

        $this->actingAs($user);
    }

    /** @group hostels */
    public function testAuthorizedUserCanViewAllHostels()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Browse']));

        Hostel::factory(2)->create();

        $response = $this->get(route('hostels.index'));

        $response->assertOk();

        $response->assertViewIs('hostels.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('hostels');
        
    }

    /** @group hostels */
    public function testAuthorizedUserCanCreateAHostel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Create']));

        /** @var array */
        $payload = Hostel::factory()->make()->toArray();

        Livewire::test(Hostels::class)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->call('createHostel');

        $hostel = Hostel::first();

        $this->assertNotNull($hostel);

        $this->assertEquals($payload['name'], $hostel->name);
        
        $this->assertEquals($payload['description'], $hostel->description);
    }

    /** @group hostels */
    public function testAuthorizationUserCanUpdateHostelDetails()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Update']));

        /** @var Hostels */
        $hostel = Hostel::factory()->create();

        /** @var array */
        $payload = Hostel::factory()->make([
            'name' => 'Sarturn'
        ])->toArray();

        Livewire::test(Hostels::class)
            ->call('editHostel', $hostel)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->call('updateHostel');

        $this->assertEquals($payload['name'], $hostel->fresh()->name);
        
        $this->assertEquals($payload['description'], $hostel->fresh()->description);
        
    }

    /** @group hostels */
    public function testAuthorizedUserCanDeleteAHostel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Delete']));

        /** @var Hostels */
        $hostel = Hostel::factory()->create();

        Livewire::test(Hostels::class)
            ->call('showDeleteHostelModal', $hostel)
            ->call('deleteHostel');

        $this->assertSoftDeleted($hostel);
        
    }

    /** @group hostels */
    public function testAuthorizedUserCanRestoreADeleteHostel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Restore']));

        /** @var Hostel */
        $hostel = Hostel::factory()->create();

        $hostel->delete();

        $this->assertSoftDeleted($hostel);

        Livewire::test(Hostels::class)->call('restoreHostel', $hostel->id);

        $this->assertNotSoftDeleted($hostel);
        
    }

    /** @group hostels */
    public function testAuthorizedUserCanCompletelyDeleteAHostel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Destroy']));

        /** @var Hostel */
        $hostel = Hostel::factory()->create();

        $hostel->delete();

        $this->assertSoftDeleted($hostel);

        Livewire::test(Hostels::class)->call('destroyHostel', $hostel->id);

        $this->assertModelMissing($hostel);
        
    }

    /** @group hostels */
    public function testAuthorizedUserCanVisitHostelShwPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Hostels Read']));

        /** @var Hostel */
        $hostel = Hostel::factory()->create();

        $response = $this->get(route('hostels.show', $hostel));

        $response->assertOk();

        $response->assertViewIs('hostels.show');

        $response->assertViewHasAll(['hostel']);
        
    }
}
