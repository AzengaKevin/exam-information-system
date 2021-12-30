<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use App\Http\Livewire\TeacherSubjects;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeachersSubjectsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group teacher-subjects */
    public function testAuthorizedUserCanUpdateTeacherSubjects()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'password' => Hash::make('password')
        ]);

        $subjects = Subject::factory(2)->create()->pluck('id')->toArray();

        $payload = array();
        
        foreach($subjects as $subject){
            $payload[$subject] = 'true';
        }

        Livewire::test(TeacherSubjects::class, ['teacher' => $teacher])
            ->set('selectedSubjects', $payload)
            ->call('updateSubjects');

        $this->assertEquals(count($payload), $teacher->fresh()->subjects->count());
        
    }
}
