<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RolePersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group roles */
    public function testARoleCanBePersistedToTheDatabase()
    {
        $payload = [
            'name' => $this->faker->sentence(),
            'description' => $this->faker->paragraph()
        ];

        $role = Role::create($payload);

        $this->assertEquals($payload['name'], $role->name);
        $this->assertEquals($payload['description'], $role->description);
        
    }
}
