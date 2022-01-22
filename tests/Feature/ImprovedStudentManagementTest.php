<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Level;
use App\Models\Stream;
use Livewire\Livewire;
use App\Models\Student;
use App\Models\LevelUnit;
use App\Models\Permission;
use App\Http\Livewire\Students;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;

class ImprovedStudentManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @var Role */
    private $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
    }

    /** @group students */
    public function testAStudentWithASingleGuardianCanBePersistedToTheSystem()
    {
        $this->withExceptionHandling();

        Notification::fake();
        
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

        /** @var Level */
        $level = Level::factory()->create();

        /** @var Stream */
        $stream = Stream::factory()->create();

        LevelUnit::create([
            'level_id' => $level->id,
            'stream_id' => $stream->id,
            'alias' => "{$level->numeric}{$stream->alias}"
        ]);

        $payload = array();

        $payload['guardian'] = [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        $payload['student'] = Student::factory([
            'admission_level_id' => $level->id,
            'stream_id' => $stream->id,
        ])->make()->toArray();

        Livewire::test(Students::class)
            ->set('guardian', $payload['guardian'])
            ->set('student', $payload['student'])
            ->call('newAddStudent');

        /** @var Student */
        $student = Student::first();

        $this->assertNotNull($student);

        $this->assertEquals($payload['student']['adm_no'], $student->adm_no);
        $this->assertEquals($payload['student']['name'], $student->name);
        $this->assertEquals($payload['student']['gender'], $student->gender);
        $this->assertEquals($payload['student']['admission_level_id'], $student->admission_level_id);
        $this->assertEquals($payload['student']['level_id'], $student->level_id);
        $this->assertEquals($payload['student']['hostel_id'], $student->hostel_id);

        $this->assertNotNull($student->level_unit_id);

        /** @var Student */
        $guardian = $student->guardians()->first();

        $this->assertNotNull($guardian);

        $this->assertEquals($payload['guardian']['profession'], $guardian->profession);
        $this->assertEquals($payload['guardian']['location'], $guardian->location);

        $this->assertNotNull($guardian->auth);

        $this->assertEquals($payload['guardian']['name'], $guardian->auth->name);
        $this->assertEquals($payload['guardian']['email'], $guardian->auth->email);
        
    }


    /** @group students */
    public function testAStudentWithASingleGuardianWithoutEmailAddressCanBePersistedToTheSystem()
    {
        $this->withExceptionHandling();

        Notification::fake();
        
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Students Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Guardians Create']));
        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Users Create']));

        /** @var Level */
        $level = Level::factory()->create();

        /** @var Stream */
        $stream = Stream::factory()->create();

        LevelUnit::create([
            'level_id' => $level->id,
            'stream_id' => $stream->id,
            'alias' => "{$level->numeric}{$stream->alias}"
        ]);

        $payload = array();

        $payload['guardian'] = [
            'name' => $this->faker->name(),
            'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
            'location' => $this->faker->streetAddress(),
            'profession' => $this->faker->company()
        ];

        $payload['student'] = Student::factory([
            'admission_level_id' => $level->id,
            'stream_id' => $stream->id,
        ])->make()->toArray();

        Livewire::test(Students::class)
            ->set('guardian', $payload['guardian'])
            ->set('student', $payload['student'])
            ->call('newAddStudent');

        /** @var Student */
        $student = Student::first();

        $this->assertNotNull($student);

        $this->assertEquals($payload['student']['adm_no'], $student->adm_no);
        $this->assertEquals($payload['student']['name'], $student->name);
        $this->assertEquals($payload['student']['gender'], $student->gender);
        $this->assertEquals($payload['student']['admission_level_id'], $student->admission_level_id);
        $this->assertEquals($payload['student']['level_id'], $student->level_id);
        $this->assertEquals($payload['student']['hostel_id'], $student->hostel_id);

        $this->assertNotNull($student->level_unit_id);

        /** @var Student */
        $guardian = $student->guardians()->first();

        $this->assertNotNull($guardian);

        $this->assertEquals($payload['guardian']['profession'], $guardian->profession);
        $this->assertEquals($payload['guardian']['location'], $guardian->location);

        $this->assertNotNull($guardian->auth);

        $this->assertEquals($payload['guardian']['name'], $guardian->auth->name);
        
    }    
}
