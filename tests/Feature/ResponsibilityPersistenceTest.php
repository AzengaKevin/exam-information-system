<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Str;
use App\Models\Responsibility;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponsibilityPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group responsibilities */
    public function testAResponsibilityCanBePersisted()
    {
        $this->withoutExceptionHandling();

        $payload = Responsibility::factory()->make()->toArray();

        $responsibility = Responsibility::create($payload);

        $this->assertEquals($payload['name'], $responsibility->name);
        $this->assertEquals(Str::slug($payload['name']), $responsibility->slug);
        $this->assertEquals($payload['description'], $responsibility->description);
        
    }
}
