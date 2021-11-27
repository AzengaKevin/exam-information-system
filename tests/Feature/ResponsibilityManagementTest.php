<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResponsibilityManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->be($user);

    }

    /** @group responsibilities */
    public function testAuthorizedUserCanVisitResponsibilitiesPage()
    {
        $this->withoutExceptionHandling();

        $response = $this->get(route('responsibilities.index'));

        $response->assertOk();

        $response->assertViewIs('responsibilities.index');

        $response->assertSeeLivewire('responsibilities');
        
    }
}
