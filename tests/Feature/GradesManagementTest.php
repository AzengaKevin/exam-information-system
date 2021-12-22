<?php

namespace Tests\Feature;

use App\Http\Livewire\Grades;
use App\Models\Grade;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class GradesManagementTest extends TestCase
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

    /** @group grades */
    public function testAUserCanVisitGradesPage()
    {
        $this->withoutExceptionHandling();

        Grade::factory(2)->create();

        $response = $this->get(route('grades.index'));

        $response->assertOk();

        $response->assertViewIs('grades.index');

        $response->assertSeeLivewire('grades');
        
    }

    /** @group grades */
    public function testAnAuthorizedUserCanUpdateAGrade()
    {

        $this->withoutExceptionHandling();
        
        /** @var Grade */
        $grade = Grade::factory()->create();

        $payload = Grade::factory()->make([
            'grade' => null
        ])->toArray();

        Livewire::test(Grades::class)
            ->call('editGrade', $grade)
            ->set('points', $payload['points'])
            ->set('english_comment', $payload['english_comment'])
            ->set('swahili_comment', $payload['swahili_comment'])
            ->set('ct_comment', $payload['ct_comment'])
            ->set('p_comment', $payload['p_comment'])
            ->call('updateGrade');

        $this->assertEquals($payload['points'], $grade->fresh()->points);
        $this->assertEquals($payload['english_comment'], $grade->fresh()->english_comment);
        $this->assertEquals($payload['swahili_comment'], $grade->fresh()->swahili_comment);
        
    }
}
