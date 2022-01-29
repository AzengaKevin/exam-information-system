<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Stream;
use Livewire\Livewire;
use App\Models\Subject;
use App\Models\Permission;
use App\Http\Livewire\Streams;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    /** @group streams */
    public function testAuthorizedUserCanBrowseStreams()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Browse']));

        Stream::factory(2)->create();

        $response = $this->get(route('streams.index'));

        $response->assertOk();

        $response->assertViewIs('streams.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('streams');
        
    }

    /** @group streams */
    public function testAuthorizedUserCanCreateAStream()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Create']));

        $payload = Stream::factory()->make()->toArray();

        Livewire::test(Streams::class)
            ->set('name', $payload['name'])
            ->set('alias', $payload['alias'])
            ->set('description', $payload['description'])
            ->call('createStream');

        $stream = Stream::first();

        $this->assertNotNull($stream);

        $this->assertEquals($payload['name'], $stream->name);
        $this->assertEquals($payload['alias'], $stream->alias);
        $this->assertEquals($payload['description'], $stream->description);
        
    }


    /** @group streams */
    public function testAuthorizedUserCanCreateAStreamAndLinkOptionalSubjects()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Create']));

        $this->artisan('db:seed --class=SubjectsSeeder');
        
        DB::table('subjects')->latest()->limit(2)->update(['optional' => true]);

        $subjectsIds = Subject::latest()->limit(2)->pluck('id')->all();

        $payload = Stream::factory()->make([
            'selectedOptionalSubjects' => array_fill_keys($subjectsIds, 'true')
        ])->toArray();

        Livewire::test(Streams::class)
            ->set('name', $payload['name'])
            ->set('alias', $payload['alias'])
            ->set('selectedOptionalSubjects', $payload['selectedOptionalSubjects'])
            ->set('description', $payload['description'])
            ->call('createStream');

        /** @var Stream */
        $stream = Stream::first();

        $this->assertNotNull($stream);

        $this->assertEquals($payload['name'], $stream->name);
        $this->assertEquals($payload['alias'], $stream->alias);
        $this->assertEquals($payload['description'], $stream->description);
        $this->assertEquals($subjectsIds, $stream->optionalSubjects->pluck('id')->toArray());
        
    }    


    /** @group streams */
    public function testAuthorizedUserCanUpdateAStream()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Update']));

        /** @var Stream */
        $stream = Stream::factory()->create();

        $payload = Stream::factory()->make()->toArray();

        Livewire::test(Streams::class)
            ->call('editStream', $stream)
            ->set('name', $payload['name'])
            ->set('alias', $payload['alias'])
            ->set('description', $payload['description'])
            ->call('updateStream');

        $this->assertEquals($payload['name'], $stream->fresh()->name);
        $this->assertEquals($payload['alias'], $stream->fresh()->alias);
        $this->assertEquals($payload['description'], $stream->fresh()->description);
        
    }

    /** @group streams */
    public function testAuthorizedUserCanUpdateAStreamWithOptionalSubjects()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Update']));

        /** @var Stream */
        $stream = Stream::factory()->create();

        $this->artisan('db:seed --class=SubjectsSeeder');
        
        DB::table('subjects')->latest()->limit(2)->update(['optional' => true]);

        $subjectsIds = Subject::latest()->limit(2)->pluck('id')->all();

        $payload = Stream::factory()->make([
            'selectedOptionalSubjects' => array_fill_keys($subjectsIds, 'true')
        ])->toArray();

        Livewire::test(Streams::class)
            ->call('editStream', $stream)
            ->set('name', $payload['name'])
            ->set('alias', $payload['alias'])
            ->set('selectedOptionalSubjects', $payload['selectedOptionalSubjects'])
            ->set('description', $payload['description'])
            ->call('updateStream');

        $this->assertEquals($payload['name'], $stream->fresh()->name);
        $this->assertEquals($payload['alias'], $stream->fresh()->alias);
        $this->assertEquals($payload['description'], $stream->fresh()->description);
        $this->assertEquals($subjectsIds, $stream->fresh()->optionalSubjects->pluck('id')->toArray());
        
    }

    /** @group streams */
    public function testAuthorizedUserCanDeleteAStream()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Delete']));

        /** @var Stream */
        $stream = Stream::factory()->create();

        Livewire::test(Streams::class)
            ->call('showDeleteStreamModal', $stream)
            ->call('deleteStream');

        $this->assertSoftDeleted($stream);
        
    }


    /** @group streams */
    public function testAuthorizedUserCanTruncateStreams()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Bulk Delete']));

        Stream::factory(2)->create();
        
        Livewire::test(Streams::class)->call('truncateStreams');

        $this->assertEquals(0, Stream::count());
    }

    /** @group streams */
    public function testAuthorizedUserCanRestoreADeletedStream()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Restore']));

        /** @var Stream */
        $stream = Stream::factory()->create();

        $stream->delete();

        Livewire::test(Streams::class)->call('restoreStream', $stream->id);

        $this->assertFalse($stream->fresh()->trashed());
        
    }


    /** @group streams */
    public function testAuthorizedUserCanCompletelyDeletedAStream()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Streams Destroy']));

        /** @var Stream */
        $stream = Stream::factory()->create();

        $stream->delete();

        Livewire::test(Streams::class)->call('destroyStream', $stream->id);
        
        $this->assertTrue(Stream::where('id', $stream->id)->withTrashed()->doesntExist());
        
    }    
}
