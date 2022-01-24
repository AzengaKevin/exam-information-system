<?php

namespace Tests\Feature;

use App\Models\Hostel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HostelPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group hostels */
    public function testAHostelCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        /** @var array */
        $payload = Hostel::factory()->make()->toArray();

        Hostel::create($payload);

        $hostel = Hostel::first();

        $this->assertEquals($payload['name'], $hostel->name);
        
        $this->assertEquals($payload['description'], $hostel->description);
        
    }
}
