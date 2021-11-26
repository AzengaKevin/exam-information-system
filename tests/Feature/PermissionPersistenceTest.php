<?php

namespace Tests\Feature;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionPersistenceTest extends TestCase
{
    
    use RefreshDatabase, WithFaker;
    use RefreshDatabase, WithFaker;

    /** @group permissions */
    public function testAPermissionCanBePersistedToTheDatabase()
    {
        $payload = [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph()
        ];

        $permission = Permission::create($payload);

        $this->assertEquals($payload['name'], $permission->name);
        $this->assertEquals($payload['description'], $permission->description);
        
    }
}
