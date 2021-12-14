<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Level;
use App\Models\Stream;
use Livewire\Livewire;
use App\Models\LevelUnit;
use App\Models\Permission;
use App\Http\Livewire\LevelUnits;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelUnitsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var Role */
    private $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
        
    }

    /** @group level-units */
    public function testAuthorizedUserCanVisitLevelUnitsManagmentPage()
    {
        $this->withoutExceptionHandling();

        LevelUnit::factory(2)->create();

        $response = $this->get(route('level-units.index'));

        $response->assertOk();

        $response->assertViewIs('level-units.index');

        $response->assertSeeLivewire('level-units');
        
    }

    /** @group level-units */
    public function testAuthorizedUserCanCreateALevelUnit()
    {
        $this->withoutExceptionHandling();

        $payload = LevelUnit::factory()->make()->toArray();

        Livewire::test(LevelUnits::class)
            ->set('level_id', $payload['level_id'])
            ->set('stream_id', $payload['stream_id'])
            ->set('description', $payload['description'])
            ->call('addLevelUnit');

            /** @var LevelUnit */
        $levelUnit = LevelUnit::first();

        $this->assertNotNull($levelUnit);

        $this->assertEquals($payload['level_id'], $levelUnit->level_id);
        $this->assertEquals($payload['stream_id'], $levelUnit->stream_id);
        $this->assertEquals($payload['description'], $levelUnit->description);
        
    }

    /** @group level-units */
    public function testAuthorizedUserCanUpdateLevelUnit()
    {
        $this->withoutExceptionHandling();

        $levelUnit = LevelUnit::factory()->create();

        $payload = LevelUnit::factory()->make()->toArray();

        Livewire::test(LevelUnits::class)
            ->call('editLevelUnit', $levelUnit)
            ->set('level_id', $payload['level_id'])
            ->set('stream_id', $payload['stream_id'])
            ->set('alias', $payload['alias'])
            ->set('description', $payload['description'])
            ->call('updateLevelUnit');

        $this->assertEquals($payload['level_id'], $levelUnit->fresh()->level_id);
        $this->assertEquals($payload['stream_id'], $levelUnit->fresh()->stream_id);
        $this->assertEquals($payload['alias'], $levelUnit->fresh()->alias);
        $this->assertEquals($payload['description'], $levelUnit->fresh()->description);

    }

    /** @group level-units */
    public function testAuthorizedUserCanDeleteALevelUnit()
    {
        $this->withoutExceptionHandling();

        $levelUnit = LevelUnit::factory()->create();

        Livewire::test(LevelUnits::class)
            ->call('showDeleteLevelUnitModal', $levelUnit)
            ->call('deleteLevelUnit');

        $this->assertFalse(LevelUnit::where('id', $levelUnit->id)->exists());
        
    }

    /** @group level-units */
    public function testAuthorizedUserCanGenerateLevelUnits()
    {
        
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'LevelUnits Create']));

        $streamsPayload = [
            [
                'name' => 'Blue',
                'alias' => 'B'
            ],
            [
                'name' => 'Green',
                'alias' => 'G'
            ],
            [
                'name' => 'Red',
                'alias' => 'R'
            ]
        ];

        array_walk($streamsPayload, function($data){
            Stream::create($data);
        });
        
        $this->artisan('db:seed --class=LevelsSeeder');

        $this->post(route('level-units.store'));

        $this->assertEquals((Level::count() * Stream::count()), LevelUnit::count());
    }

    /** @group level-units */
    public function testAuthorizedUsersCanVisitLevelUnitsShowPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'LevelUnits Read']));

        $levelUnit = LevelUnit::factory()->create();

        $response = $this->get(route('level-units.show', $levelUnit));

        $response->assertOk();

        $response->assertViewIs('level-units.show');

        $response->assertViewHasAll(['levelUnit']);

        $response->assertSeeLivewire('level-unit-students');

        $response->assertSeeLivewire('level-unit-responsibilities');
        
    }
}