<?php

namespace Tests\Feature;

use App\Models\LevelUnit;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LevelUnitsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp() : void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create();

        $this->be($user);
    }

    /** @group level-units */
    public function testAuthorizedUserCanVisitLevelUnitsManagmentPage()
    {
        $this->withoutExceptionHandling();

        LevelUnit::factory(2)->create();

        $response = $this->get(route('level-units.index'));

        $response->assertOk();

        $response->assertViewIs('level-units.index');

        $response->assertSeeLivewire('level-units');
        
    }
}
