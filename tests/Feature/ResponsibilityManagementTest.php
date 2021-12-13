<?php

namespace Tests\Feature;

use App\Http\Livewire\Responsibilities;
use App\Models\Responsibility;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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

        Responsibility::factory(2)->create();

        $response = $this->get(route('responsibilities.index'));

        $response->assertOk();

        $response->assertViewIs('responsibilities.index');

        $response->assertSeeLivewire('responsibilities');
        
    }

    /** @group respnsibilities */
    public function testAuthorizedUserCanCreateAResponsibility()
    {
        $this->withoutExceptionHandling();
        
        $payload = Responsibility::factory()->make()->toArray();

        Livewire::test(Responsibilities::class)
            ->set('name', $payload['name'])
            ->set('requirements', $payload['requirements'])
            ->set('description', $payload['description'])
            ->call('createResponsibility');

        $responsibility = Responsibility::first();

        $this->assertNotNull($responsibility);
        
        $this->assertEquals($payload['name'], $responsibility->name);
        $this->assertEquals($payload['requirements'], $responsibility->requirements);
        $this->assertEquals($payload['description'], $responsibility->description);

    }
}
