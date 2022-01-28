<?php

namespace Tests\Feature;

use App\Models\Exam;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamActivitiesManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @var Teacher */
    private $teacher;

    private $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        $this->teacher = Teacher::factory()->create();

        /** @var Authenticatable */
        $user = $this->teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'role_id' => $this->role->id,
            'password' => Hash::make('password')
        ]);
        
        $this->actingAs($user);
        
    }
    
    /**
     * @group exam-activities
     */
    public function testAuthorizedUsersCanVisitExamActivitiesPage()
    {

        $this->withoutExceptionHandling();

        $exam = Exam::factory()->create();

        $response = $this->get(route('exams.activities.index', $exam));

        $response->assertOk();

        $response->assertViewIs('exams.activities.index');

        $response->assertViewHasAll(['exam', 'users']);
        
    }
}
