<?php

namespace Tests\Feature;

use App\Http\Livewire\BulkMessages\GroupedGuardians;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\Guardian;
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

    /** @grou messages */
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
            });

        Livewire::test(GroupedGuardians::class)
            ->set('groupBy', 'all')
            ->set('content', $this->faker->sentence())
            ->call('sendMessages');

        $this->assertEquals(Guardian::count(), Message::count());
        
    }
}
