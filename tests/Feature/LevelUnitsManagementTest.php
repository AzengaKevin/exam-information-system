<?php

namespace Tests\Feature;

use App\Http\Livewire\LevelUnits;
use App\Models\LevelUnit;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class LevelUnitsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->be($user);
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
            ->set('alias', $payload['alias'])
            ->set('description', $payload['description'])
            ->call('addLevelUnit');

            /** @var LevelUnit */
        $levelUnit = LevelUnit::first();

        $this->assertNotNull($levelUnit);

        $this->assertEquals($payload['level_id'], $levelUnit->level_id);
        $this->assertEquals($payload['stream_id'], $levelUnit->stream_id);
        $this->assertEquals($payload['alias'], $levelUnit->alias);
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
}
