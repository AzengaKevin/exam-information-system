<?php

namespace Tests\Feature;

use App\Models\Responsibility;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeachersResponsibilitiesManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group teachers-responsibilities */
    public function testAuthorizedUserCanVisitTeacherResponsibilityManagementPage()
    {
        $this->withoutExceptionHandling();
            
        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);

        $responsibilities = Responsibility::factory(2)->create()->pluck('id')->toArray();

        $teacher->responsibilities()->sync($responsibilities);

        $response = $this->get(route('teachers.responsibilities.index', $teacher));

        $response->assertOk();

        $response->assertViewIs('teachers.responsibilities.index');

        $response->assertViewHasAll(['teacher']);

        $response->assertSeeLivewire('teacher-responsibilities');
        
    }
}
