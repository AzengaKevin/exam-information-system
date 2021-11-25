<?php

namespace Tests\Feature;

use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TeacherPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group teacher */
    public function testATecherCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Teacher::factory()->make()->toArray();

        /** @var Teacher */
        $teacher = Teacher::create($payload);

        $teacher->auth()->create([
            'name' => $name = $this->faker->name(),
            'email' => $email = $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);

        $this->assertEquals($payload['employer'], $teacher->employer);

        $this->assertNotNull($teacher->fresh()->auth);

        $this->assertEquals($name, $teacher->fresh()->auth->name);
        $this->assertEquals($email, $teacher->fresh()->auth->email);
        
    }
}
