<?php

namespace Tests\Feature;

use App\Models\Subject;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group subjects */
    public function testAuthorizedUsersCanVisitSubjectsPage()
    {
        $this->withoutExceptionHandling();

        Subject::factory(2)->create();

        $response = $this->get(route('subjects.index'));

        $response->assertOk();

        $response->assertViewIs('subjects.index');

        $response->assertSeeLivewire('subjects');
        
    }

    
}
