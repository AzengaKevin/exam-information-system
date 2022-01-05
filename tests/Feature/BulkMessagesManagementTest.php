<?php

namespace Tests\Feature;

use App\Http\Livewire\BulkMessages\GroupedGuardians;
use App\Http\Livewire\BulkMessages\RandomizedGuardians;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

class BulkMessagesManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        $this->role = Role::factory()->create();

        /** @var Authenticatable */
        $user = User::factory()->create(['role_id' => $this->role->id]);

        $this->actingAs($user);
    }

    /** @group messages */
    public function testMessagesCanBeSentToAllGuardianInTheGroupedGuardiansComponent()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        Student::factory(2)->create()
            ->each(function(Student $student){

                /** @var Guardian */
                $guardian = Guardian::factory()->create();

                $guardian->auth()->create([
                    'name' => $this->faker->name(),
                    'email' => $this->faker->safeEmail(),
                    'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                    'password' => Hash::make('password')
                ]);

                $student->guardians()->attach($student);
            });

        Livewire::test(GroupedGuardians::class)
            ->set('groupBy', 'all')
            ->set('content', $this->faker->sentence())
            ->call('sendMessages');

        $this->assertEquals(Guardian::count(), Message::count());
        
    }

    /** @group messages */
    public function testMessagesCanBeSentToLevelGuardians()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        /** @var Level */
        $level = Level::factory()->create();

        Student::factory(2)->create(['level_id' => $level->id])
            ->each(function(Student $student){
                /** @var Guardian */
                $guardian = Guardian::factory()->create();

                $guardian->auth()->create([
                    'name' => $this->faker->name(),
                    'email' => $this->faker->safeEmail(),
                    'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                    'password' => Hash::make('password')
                ]);

                $student->guardians()->attach($guardian);
            });

        $selectedLevels = array(
            $level->id => 'true'
        );

        Livewire::test(GroupedGuardians::class)
            ->set('groupBy', 'levels')
            ->set('selectedLevels', $selectedLevels)
            ->set('content', $this->faker->sentence())
            ->call('sendMessages');

        $this->assertEquals($level->students()->count(), Message::count());
        
    }


    /** @group messages */
    public function testMessagesCanBeSentToStreamGuardians()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        /** @var LevelUnit */
        $levelUnit = LevelUnit::factory()->create();

        Student::factory(2)->create(['level_unit_id' => $levelUnit->id])
            ->each(function(Student $student){
                /** @var Guardian */
                $guardian = Guardian::factory()->create();

                $guardian->auth()->create([
                    'name' => $this->faker->name(),
                    'email' => $this->faker->safeEmail(),
                    'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                    'password' => Hash::make('password')
                ]);

                $student->guardians()->attach($guardian);
            });

        $selectedLevelUnits = array(
            $levelUnit->id => 'true'
        );

        Livewire::test(GroupedGuardians::class)
            ->set('groupBy', 'streams')
            ->set('selectedLevelUnits', $selectedLevelUnits)
            ->set('content', $this->faker->sentence())
            ->call('sendMessages');

        $this->assertEquals($levelUnit->students()->count(), Message::count());
        
    }

    /** @group messages */
    public function testAMessageCanBeSentToRandomizedGuardianBasedOnSTudent()
    {
        $this->withoutExceptionHandling();

        Notification::fake();

        Student::factory(2)->create()
            ->each(function(Student $student){
                /** @var Guardian */
                $guardian = Guardian::factory()->create();

                $guardian->auth()->create([
                    'name' => $this->faker->name(),
                    'email' => $this->faker->safeEmail(),
                    'phone' => $this->faker->randomElement(['1', '7']) . $this->faker->numberBetween(10000000, 99999999),
                    'password' => Hash::make('password')
                ]);

                $student->guardians()->attach($guardian);

            });

        $studentsIds = Student::all(['id'])->pluck('id')->toArray();

        $payload = array();

        foreach($studentsIds as $id){
            $payload[$id] = 'true' ;
        }

        Livewire::test(RandomizedGuardians::class)
            ->set('selectedStudents', $payload)
            ->set('content', $this->faker->sentence())
            ->call('sendMessages');

        $this->assertEquals(Student::count(), Message::count());
        
    }

}
