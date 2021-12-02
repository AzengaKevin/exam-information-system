<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\Role;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamsAnalysisManagementTest extends TestCase
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

    /** @group exams-analysis */
    public function testAuthorizedUserCanVisitiExamAnalysisPage()
    {
        $this->withoutExceptionHandling();

        $exam = Exam::factory()->create();

        $response = $this->get(route('exams.analysis.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.analysis.index');

        $response->assertViewHasAll(['exam']);
        
    }
}
