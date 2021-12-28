<?php

namespace Tests\Feature;

use App\Http\Livewire\Streams;
use App\Models\Level;
use App\Models\Permission;
use Tests\TestCase;
use App\Models\Role;
use App\Models\Stream;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

class StreamsManagementTest extends TestCase
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
    public function testAuthorizedUserCanBrowseStreams()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Browse']));

        Stream::factory(2)->create();

        $response = $this->get(route('streams.index'));

        $response->assertOk();

        $response->assertViewIs('streams.index');

        $response->assertSeeLivewire('streams');
        
    }

    /** @group levels */
    public function testAuthorizedUserCanTruncateStreams()
    {
        $this->withoutExceptionHandling();

        Stream::factory(2)->create();
        
        Livewire::test(Streams::class)->call('truncateStreams');

        $this->assertEquals(0, Stream::count());
    }
}
