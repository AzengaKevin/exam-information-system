<?php

namespace Tests\Feature;

use App\Models\Grading;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GradingPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group gradings */
    public function testAGradingSystemCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Grading::factory()->make()->toArray();

        Grading::create($payload);

        $grading = Grading::first();

        $this->assertNotNull($grading);

        $this->assertEquals($payload["name"], $grading->name);

        $this->assertTrue(is_array($grading->values));
        
    }
}
