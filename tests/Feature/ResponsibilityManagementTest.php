<?php

namespace Tests\Feature;

use App\Http\Livewire\Responsibilities;
use App\Models\Permission;
use App\Models\Responsibility;
use App\Models\Role;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class ResponsibilityManagementTest extends TestCase
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

        $this->be($user);

    }

    /** @group responsibilities */
    public function testAuthorizedUserCanVisitResponsibilitiesPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Browse']));

        Responsibility::factory(2)->create();

        $response = $this->get(route('responsibilities.index'));

        $response->assertOk();

        $response->assertViewIs('responsibilities.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('responsibilities');
        
    }

    /** @group respnsibilities */
    public function testAuthorizedUserCanCreateAResponsibility()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Create']));
        
        $payload = Responsibility::factory()->make()->toArray();

        Livewire::test(Responsibilities::class)
            ->set('name', $payload['name'])
            ->set('requirements', $payload['requirements'])
            ->set('description', $payload['description'])
            ->call('createResponsibility');

        $responsibility = Responsibility::first();

        $this->assertNotNull($responsibility);
        
        $this->assertEquals($payload['name'], $responsibility->name);
        $this->assertEquals($payload['requirements'], $responsibility->requirements);
        $this->assertEquals($payload['description'], $responsibility->description);
    }

    /** @group responsibilities */
    public function testAuthorizedUserCanUpdateAResponsibility()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Update']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory()->create();

        $payload = Responsibility::factory()->make(['how_many' => 2])->toArray();

        Livewire::test(Responsibilities::class)
            ->call('editResponsibility', $responsibility)
            ->set('name', $payload['name'])
            ->set('how_many', $payload['how_many'])
            ->set('description', $payload['description'])
            ->set('requirements', $payload['requirements'])
            ->call('updateResponsibility');
        
        $this->assertEquals($payload['name'], $responsibility->fresh()->name);
        $this->assertEquals($payload['how_many'], $responsibility->fresh()->how_many);
        $this->assertEquals($payload['requirements'], $responsibility->fresh()->requirements);
        $this->assertEquals($payload['description'], $responsibility->fresh()->description);

    }

    /** @group responsibilities */
    public function testUnAuthorizedUserCantUpdateALockedResponsibility()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Update']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory(['locked' => true])->create();

        $payload = Responsibility::factory()->make()->toArray();

        Livewire::test(Responsibilities::class)
            ->call('editResponsibility', $responsibility)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->set('requirements', $payload['requirements'])
            ->call('updateResponsibility');
        
        $this->assertEquals($responsibility->name, $responsibility->fresh()->name);
        $this->assertEquals($responsibility->requirements, $responsibility->fresh()->requirements);
        $this->assertEquals($responsibility->description, $responsibility->fresh()->description);
        
    }


    /** @group responsibilities */
    public function testAuthorizedUserCanUpdateALockedResponsibility()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Update']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Update Locked']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory(['locked' => true])->create();

        $payload = Responsibility::factory()->make()->toArray();

        Livewire::test(Responsibilities::class)
            ->call('editResponsibility', $responsibility)
            ->set('name', $payload['name'])
            ->set('description', $payload['description'])
            ->set('requirements', $payload['requirements'])
            ->call('updateResponsibility');
        
        $this->assertEquals($payload['name'], $responsibility->fresh()->name);
        $this->assertEquals($payload['requirements'], $responsibility->fresh()->requirements);
        $this->assertEquals($payload['description'], $responsibility->fresh()->description);
        
    }

    /** @group responsibilities */
    public function testAuthorizedUserCanToggleLockStatusOfAResponsibility()
    {
        $this->withoutExceptionHandling();
        
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Update Locked']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory(['locked' => true])->create();

        Livewire::test(Responsibilities::class)->call('toggleResponsibilityLock', $responsibility);

        $this->assertFalse($responsibility->fresh()->locked);
    }

    /** @group responsibilities */
    public function testAuthorizedUserCanDeleteAResponsibility()
    {
        $this->withoutExceptionHandling();
        
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Delete']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory()->create();

        Livewire::test(Responsibilities::class)
            ->call('showDeleteResponsibilityModal', $responsibility)
            ->call('deleteResponsibility');

        $this->assertSoftDeleted($responsibility);

    }

    /** @group responsibilities */
    public function testAuthorizedUserCanRestoreADeletedResponsibility()
    {
        $this->withoutExceptionHandling();
        
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Restore']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory()->create();

        $responsibility->delete();

        $this->assertSoftDeleted($responsibility);

        Livewire::test(Responsibilities::class)->call('restoreResponsibility', $responsibility->id);

        $this->assertFalse($responsibility->fresh()->trashed());
    }

    /** @group responsibilities */
    public function testAuthorizedUserCanCompletelyDeletedAResponsibility()
    {
        $this->withoutExceptionHandling();
        
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Responsibilities Destroy']));
        
        /** @var Responsibility */
        $responsibility = Responsibility::factory()->create();

        $responsibility->delete();

        $this->assertSoftDeleted($responsibility);

        Livewire::test(Responsibilities::class)->call('destroyResponsibility', $responsibility->id);

        $this->assertTrue(Responsibility::where('id', $responsibility->id)->withTrashed()->doesntExist());
    }
}
