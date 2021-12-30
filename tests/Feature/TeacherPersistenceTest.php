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

    /** @group teachers */
    public function testATeacherCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Teacher::factory()->make()->toArray();

        /** @var Teacher */
        $teacher = Teacher::create($payload);

        $teacher->auth()->create([
            'name' => $name = $this->faker->name(),
            'email' => $email = $this->faker->safeEmail(),
            'phone' => "707427854",
            'password' => Hash::make('password')
        ]);

        $this->assertEquals($payload['employer'], $teacher->employer);

        $this->assertNotNull($teacher->fresh()->auth);

        $this->assertEquals($name, $teacher->fresh()->auth->name);
        $this->assertEquals($email, $teacher->fresh()->auth->email);
        
    }
}
