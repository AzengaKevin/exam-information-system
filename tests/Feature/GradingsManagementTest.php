<?php

namespace Tests\Feature;

use App\Http\Livewire\Gradings;
use App\Models\Grading;
use App\Models\Permission;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class GradingsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
    }

    /** @group gradings */
    public function testAUserCanVisitGradingsPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Gradings Browse']));

        Grading::factory(2)->create();

        $response = $this->get(route('gradings.index'));

        $response->assertOk();

        $response->assertViewIs('gradings.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('gradings');
        
    }

    /** @group gradings */
    public function testAuthorizedUserCanCreateAGradingSystem()
    {
        $this->withoutExceptionHandling();

        $payload = Grading::factory()->make()->toArray();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Gradings Create']));

        Livewire::test(Gradings::class)
            ->set('name', $payload['name'])
            ->set('values', $payload['values'])
            ->call('addGrading');

        /** @var Grading */
        $grading = Grading::first();

        $this->assertNotNull($grading);

        $this->assertEquals($payload["name"], $grading->name);

        $this->assertTrue(is_array($grading->values));
        
    }

    /** @group gradings */
    public function testAuthorizedUserCanUpdateAGradingSystem()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Gradings Update']));

        /** @var Grading */
        $grading = Grading::factory()->create();

        $payload = Grading::factory()->make()->toArray();

        Livewire::test(Gradings::class)
            ->call('editGrading', $grading)
            ->set('name', $payload['name'])
            ->set('values', $payload['values'])
            ->call('updateGrading');

        $this->assertEquals($payload["name"], $grading->fresh()->name);

        $this->assertTrue(is_array($grading->fresh()->values));
        
    }

    /** @group gradings */
    public function testAuthorizedUserCanDeleteGradingSystem()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Gradings Delete']));

        /** @var Grading */
        $grading = Grading::factory()->create();

        Livewire::test(Gradings::class)
            ->call('showDeleteGradingModal', $grading)
            ->call('deleteGrading');

        $this->assertFalse(Grading::where('id', $grading->id)->exists());
        
    }

    /** @group gradings */
    public function testAuthorizedCanViewGradingsValues()
    {
        $this->withoutExceptionHandling();

        /** @var Grading */
        $grading = Grading::factory()->create();

        Livewire::test(Gradings::class)->call('showGrading', $grading);
    }

    /** @group gradings */
    public function testAuthorizedUserCanRestoreADeleteGradingSystem()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Gradings Restore']));

        /** @var Grading */
        $grading = Grading::factory()->create();

        $grading->delete();

        Livewire::test(Gradings::class)->call('restoreGrading', $grading->id);

        $this->assertFalse($grading->fresh()->trashed());
        
    }

    /** @group gradings */
    public function testAuthorizedUserCanCompletelyDeleteAGradingSystem()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Gradings Destroy']));

        /** @var Grading */
        $grading = Grading::factory()->create();

        $grading->delete();

        Livewire::test(Gradings::class)->call('destroyGrading', $grading->id);

        $this->assertTrue(Grading::where('id', $grading->id)->withTrashed()->doesntExist());
        
    }
}
