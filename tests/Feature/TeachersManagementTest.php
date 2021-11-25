<?php

namespace Tests\Feature;

use App\Http\Livewire\Teachers;
use Tests\TestCase;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class TeachersManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->be($user);
    }

    /** @group teachers */
    public function testAuthorizedUserCanVisitTeachersPage()
    {
        $this->withoutExceptionHandling();

        for ($i=0; $i < 2; $i++) { 
            
            /** @var Teacher */
            $teacher = Teacher::factory()->create();

            $teacher->auth()->create([
                'name' => $this->faker->name(),
                'email' => $this->faker->safeEmail(),
                'password' => Hash::make('password')
            ]);
        }

        $response = $this->get(route('teachers.index'));

        $response->assertOk();

        $response->assertViewIs('teachers.index');

        $response->assertSeeLivewire('teachers');
        
    }

    /** @group teachers */
    public function testAuthorizedUserCanAddATeacher()
    {
        $this->withoutExceptionHandling();

        $payload = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'employer' => $this->faker->randomElement(Teacher::employerOptions()),
            'tsc_number' => $this->faker->numberBetween(123456, 999999),
        ];

        Livewire::test(Teachers::class)
            ->set('name', $payload['name'])
            ->set('email', $payload['email'])
            ->set('employer', $payload['employer'])
            ->set('tsc_number', $payload['tsc_number'])
            ->call('addTeacher');

        $teacher = Teacher::first();

        $this->assertNotNull($teacher);

        $this->assertEquals($payload['employer'], $teacher->employer);
        $this->assertEquals($payload['tsc_number'], $teacher->tsc_number);

        $this->assertNotNull($teacher->auth);

        $this->assertEquals($payload['name'], $teacher->auth->name);
        $this->assertEquals($payload['email'], $teacher->auth->email);
    }
}
