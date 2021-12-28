<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Level;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group levels */
    public function testALevelCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Level::factory()->make()->toArray();

        $level = Level::create($payload);

        $this->assertNotNull($level);

        $this->assertEquals($payload['name'], $level->name);
        $this->assertEquals(Str::slug($payload['name']), $level->slug);
        $this->assertEquals($payload['numeric'], $level->numeric);
        $this->assertEquals($payload['description'], $level->description);
        
    }
}
