<?php

namespace Tests\Feature;

use App\Models\Exam;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamsScoresManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        /** @var Authenticatable */
        $user = $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'role_id' => $this->role->id,
            'password' => Hash::make('password')
        ]);
        
        $this->actingAs($user);
        
    }

    /** @group exams-scores */
    public function testAuthorizedUserCanVisitiExamScoresPageAndViewOwnClasses()
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()->create();

        $response = $this->get(route('exams.scores.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.scores.index');

        $response->assertViewHasAll(['exam', 'responsibilities']);
        
    }
    
    
}
