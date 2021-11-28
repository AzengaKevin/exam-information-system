<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DepartmentPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group departments */
    public function testADepartmentCanBePersistedToTheDatabase()
    {
        $payload = Department::factory()->make()->toArray();

        $department = Department::create($payload);

        $this->assertNotNull($department);

        $this->assertEquals($payload['name'], $department->name);
        $this->assertEquals($payload['description'], $department->description);
        $this->assertEquals( Str::slug($payload['name']), $department->slug);
    }
}
