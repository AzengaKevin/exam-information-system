<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Stream;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StreamsPersistentTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group streams */
    public function testAStreamCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Stream::factory()->make()->toArray();

        $stream = Stream::create($payload);

        $this->assertEquals($payload['name'], $stream->name);
        $this->assertEquals(Str::slug($payload['name']), $stream->slug);
        $this->assertEquals($payload['alias'], $stream->alias);
        $this->assertEquals($payload['description'], $stream->description);
        
    }
}
