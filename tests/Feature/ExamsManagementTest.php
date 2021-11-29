<?php

namespace Tests\Feature;

use App\Models\Exam;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->actingAs($user);
    }

    /** @group exams */
    public function testAuthorizedUserCanVisitExamsManagementPage()
    {
        $this->withoutExceptionHandling();

        Exam::factory(2)->create();

        $response = $this->get(route('exams.index'));

        $response->assertOk();

        $response->assertViewIs('exams.index');

        $response->assertSeeLivewire('exams');
        
    }
}
