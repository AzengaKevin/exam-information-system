<?php

namespace Tests\Feature;

use App\Http\Livewire\TeacherResponsibilities;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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

    /** @group teachers-responsibilities */
    public function testAuthorizedUserCanAssignATeacherResponsibility()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);
        
        $responsibility = Responsibility::factory()->create([
            'requirements' => ['class']
        ]);

        $levelUnit = LevelUnit::factory()->create();

        Livewire::test(TeacherResponsibilities::class, ['teacher' => $teacher])
            ->set('responsibility_id', $responsibility->id)
            ->set('level_unit_id', $levelUnit->id)
            ->call('assignResponsibility');

        $this->assertEquals(1, $teacher->responsibilities()->count());

        $this->assertNotNull($teacher->fresh()->responsibilities->first()->pivot->levelUnit);
    }

    /** @group teachers-responsibilities */
    public function testAuthorizedUserCanRevokeTeacherResponsibilities()
    {
        $this->withoutExceptionHandling();

        /** @var Teacher */
        $teacher = Teacher::factory()->create();

        $teacher->auth()->create([
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'password' => Hash::make('password')
        ]);
        
        $responsibility = Responsibility::factory()->create();

        $teacher->responsibilities()->attach($responsibility);

        $this->assertEquals(1, $teacher->fresh()->responsibilities()->count());

        Livewire::test(TeacherResponsibilities::class, ['teacher' => $teacher])
            ->call('removeResponsibility', $responsibility);

        $this->assertEquals(0, $teacher->fresh()->responsibilities()->count());
        
    }
}
