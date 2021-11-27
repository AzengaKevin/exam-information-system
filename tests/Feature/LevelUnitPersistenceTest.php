<?php

namespace Tests\Feature;

use App\Models\LevelUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LevelUnitPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group level-units */
    public function testALevelUnitCanBePersisted()
    {
        
        $this->withoutExceptionHandling();

        $payload = LevelUnit::factory()->make()->toArray();

        $levelUnit = LevelUnit::create($payload);

        $this->assertNotNull($levelUnit);

        $this->assertEquals($payload['level_id'], $levelUnit->level_id);
        $this->assertEquals($payload['stream_id'], $levelUnit->stream_id);
        $this->assertEquals($payload['alias'], $levelUnit->alias);
        $this->assertEquals($payload['description'], $levelUnit->description);
        
    }
}
