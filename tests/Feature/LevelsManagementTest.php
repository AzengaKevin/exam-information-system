<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Level;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\Permission;
use Illuminate\Support\Str;
use App\Http\Livewire\Levels;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelsManagementTest extends TestCase
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

    /** @group levels */
    public function testAuthorizedUserCanBrowseLevels()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Browse']));

        Level::factory(2)->create();

        $response = $this->get(route('levels.index'));

        $response->assertOk();

        $response->assertViewIs('levels.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('levels');
        
    }

    /** @group levels */
    public function testAuthorizedCanAddALevel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Create']));

        /** @var array */
        $payload = Level::factory()->make()->toArray();

        Livewire::test(Levels::class)
            ->set('name', $payload['name'])
            ->set('numeric', $payload['numeric'])
            ->set('description', $payload['description'])
            ->call('createLevel');

        $level = Level::first();

        $this->assertNotNull($level);

        $this->assertEquals($payload['name'], $level->name);
        $this->assertEquals(Str::slug($payload['name']), $level->slug);
        $this->assertEquals($payload['numeric'], $level->numeric);
        $this->assertEquals($payload['description'], $level->description);
        
    }

    /** @group levels */
    public function testAuthorizedUserCanUpdateALevel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Update']));

        /** @var Level */
        $level = Level::factory()->create();

        /** @var array */
        $payload = Level::factory()->make()->toArray();

        Livewire::test(Levels::class)
            ->call('editLevel', $level)
            ->set('name', $payload['name'])
            ->set('numeric', $payload['numeric'])
            ->set('description', $payload['description'])
            ->call('updateLevel');

        $this->assertEquals($payload['name'], $level->fresh()->name);
        $this->assertEquals(Str::slug($payload['name']), $level->fresh()->slug);
        $this->assertEquals($payload['numeric'], $level->fresh()->numeric);
        $this->assertEquals($payload['description'], $level->fresh()->description);
        
    }

    /** @group levels */
    public function testAuthorizedUserCanTrashALevel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Delete']));

        /** @var Level */
        $level = Level::factory()->create();

        Livewire::test(Levels::class)
            ->call('showDeleteLevelModal', $level)
            ->call('deleteLevel');

        $this->assertSoftDeleted($level);
        
    }

    /** @group levels */
    public function testAuthorizedUserCanTruncateLevels()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Bulk Delete']));

        Level::factory(2)->create();
        
        Livewire::test(Levels::class)->call('truncateLevels');

        $this->assertEquals(0, Level::count());
    }

    /** @group levels */
    public function testAuthorizedUserCanVisitLevelShowPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Read']));

        /** @var Level */
        $level = Level::factory()->create();

        Student::factory(2)->create(['admission_level_id' => $level->id]);

        $response = $this->get(route('levels.show', $level));

        $response->assertOk();

        $response->assertViewIs('levels.show');

        $response->assertViewHasAll(['level', 'systemSettings']);

        $response->assertSeeLivewire('level-students');

        $response->assertSeeLivewire('level-responsibilities');
        
    }

    /** @group levels */
    public function testAuthorizedUserCanRestoreTrashedLevel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Restore']));

        /** @var Level */
        $level = Level::factory()->create();

        $level->delete();

        Livewire::test(Levels::class)->call('restoreLevel', $level->id);

        $this->assertFalse($level->fresh()->trashed());
        
    }

    /** @group levels */
    public function testAuthorizedUserCanCompletelyDeleteALevel()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Destroy']));

        /** @var Level */
        $level = Level::factory()->create();

        $level->delete();

        Livewire::test(Levels::class)->call('destroyLevel', $level->id);

        $this->assertTrue(Level::where('id', $level->id)->withTrashed()->doesntExist());
        
    }
}
