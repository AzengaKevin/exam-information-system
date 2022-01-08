<?php

namespace Tests\Feature;

use App\Http\Livewire\Levels;
use App\Models\Level;
use App\Models\Permission;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class LevelsManagementTest extends TestCase
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

    /** @group levels */
    public function testAuthorizedUserCanBrowseLevels()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Levels Browse']));

        Level::factory(2)->create();

        $response = $this->get(route('levels.index'));

        $response->assertOk();

        $response->assertViewIs('levels.index');

        $response->assertSeeLivewire('levels');
        
    }

    /** @group levels */
    public function testAuthorizedUserCanTruncateLevels()
    {
        $this->withoutExceptionHandling();

        Level::factory(2)->create();
        
        Livewire::test(Levels::class)
            ->call('truncateLevels');

        $this->assertEquals(0, Level::count());
    }

    /** @group levels */
    public function testAuthorizedUserCanVisitLevelShowPage()
    {
        $this->withoutExceptionHandling();

        /** @var Level */
        $level = Level::factory()->create();

        Student::factory(2)->create(['admission_level_id' => $level->id]);

        $response = $this->get(route('levels.show', $level));

        $response->assertOk();

        $response->assertViewIs('levels.show');

        $response->assertViewHasAll(['level', 'systemSettings']);

        $response->assertSeeLivewire('level-students');

        $response->assertSeeLivewire('level-responsibilities');
        
    }
}
