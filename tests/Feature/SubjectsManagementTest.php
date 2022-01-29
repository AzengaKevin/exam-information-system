<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Subject;
use App\Http\Livewire\Subjects;
use App\Models\Level;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubjectsManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private Role $role;

    public function setUp(): void
    {
        parent::setUp();

        /** @var Authenticatable */
        $user = User::factory()->create([
            'role_id' => $this->role = Role::factory()->create()
        ]);

        $this->actingAs($user);
    }

    /** @group subjects */
    public function testAuthorizedUsersCanVisitSubjectsPage()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Browse']));

        Subject::factory(2)->create();

        $response = $this->get(route('subjects.index'));

        $response->assertOk();

        $response->assertViewIs('subjects.index');

        $response->assertViewHasAll(['trashed']);

        $response->assertSeeLivewire('subjects');
        
    }

    /** @group subjects */
    public function testAuthorizedUsersCanAddASubject()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Create']));

        $payload = Subject::factory()->make([
            'name' => 'English',
            'shortname' => 'eng'
        ])->toArray();

        Livewire::test(Subjects::class)
            ->set('name', $payload['name'])
            ->set('shortname', $payload['shortname'])
            ->set('description', $payload['description'])
            ->set('department_id', $payload['department_id'])
            ->call('createSubject');
        
        /** @var Subject */
        $subject = Subject::first();

        $this->assertNotNull($subject);

        $this->assertEquals($payload['name'], $subject->name);
        $this->assertEquals($payload['shortname'], $subject->shortname);
        $this->assertEquals($payload['description'], $subject->description);
        $this->assertEquals($payload['department_id'], $subject->department_id);
        
    }

    /** @group subjects */
    public function testAnAuthorizedUserCanAddAnAdvancedSubject()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Create']));

        /** @var Level */
        $level = Level::factory()->create();

        $payload = Subject::factory()->make([
            'name' => 'English',
            'shortname' => 'eng',
            'segments' => [
                ['level_id' => $level->id, 'key' => 'outOf60', 'value' => 60],
                ['level_id' => $level->id, 'key' => 'comp','value' => 40]
            ]
        ])->toArray();

        Livewire::test(Subjects::class)
            ->set('name', $payload['name'])
            ->set('shortname', $payload['shortname'])
            ->set('description', $payload['description'])
            ->set('department_id', $payload['department_id'])
            ->set('segments', $payload['segments'])
            ->call('createSubject');

        /** @var Subject */
        $subject = Subject::first();

        $this->assertNotNull($subject);

        $this->assertEquals($payload['name'], $subject->name);
        $this->assertEquals($payload['shortname'], $subject->shortname);
        $this->assertEquals($payload['description'], $subject->description);
        $this->assertEquals($payload['department_id'], $subject->department_id);
        $this->assertEquals([$level->id => ['outOf60' => 60, 'comp'=> 40]], $subject->segments);
        
    }

    /** @group subjects */
    public function testAuthorizedUserCanUpdateASubject()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Update']));

        /** @var Subject */
        $subject = Subject::factory()->create();

        /** @var Level */
        $level = Level::factory()->create();
        
        $payload = Subject::factory()->make([
            'name' => 'English',
            'shortname' => 'eng',
            'segments' => [
                ['level_id' => $level->id, 'key' => 'outOf60', 'value' => 60],
                ['level_id' => $level->id, 'key' => 'comp','value' => 40]
            ]
        ])->toArray();

        Livewire::test(Subjects::class)
            ->call('editSubject', $subject)
            ->set('name', $payload['name'])
            ->set('shortname', $payload['shortname'])
            ->set('description', $payload['description'])
            ->set('department_id', $payload['department_id'])
            ->set('segments', $payload['segments'])
            ->call('updateSubject');

        $this->assertEquals($payload['name'], $subject->fresh()->name);
        $this->assertEquals($payload['shortname'], $subject->fresh()->shortname);
        $this->assertEquals($payload['description'], $subject->fresh()->description);
        $this->assertEquals($payload['department_id'], $subject->fresh()->department_id);
        $this->assertEquals([$level->id => ['outOf60' => 60, 'comp'=> 40]], $subject->fresh()->segments);
        
    }

    /** @group subjects */
    public function testAuthorizedUserCanDeleteASubject()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Delete']));

        $subject = Subject::factory()->create();

        Livewire::test(Subjects::class)
            ->call('showDeleteSubjectModal', $subject)
            ->call('deleteSubject');
        
        $this->assertSoftDeleted($subject);
        
    }

    /** @group subjects */
    public function testAuthorizedUserCanTruncateSubjects()
    {
        $this->withExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Bulk Delete']));

        Subject::factory(2)->create();

        Livewire::test(Subjects::class)->call('truncateSubjects');

        $this->assertEquals(0, Subject::count());

        $this->assertEquals(2, Subject::withTrashed()->count());
    }

    /** @group subjects */
    public function testAuthorizedUserCanRestoreTrashedSubject()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Restore']));

        /** @var Subject */
        $subject = Subject::factory()->create();

        $subject->delete();

        $this->assertSoftDeleted($subject);

        Livewire::test(Subjects::class)->call('restoreSubject', $subject->id);

        $this->assertFalse($subject->fresh()->trashed());
        
    }

    /** @group subjects */
    public function testAuthorizedUserCanDestoryASubject()
    {
        $this->withoutExceptionHandling();

        $this->role->permissions()->attach(Permission::firstOrCreate(['name' => 'Subjects Destroy']));

        /** @var Subject */
        $subject = Subject::factory()->create();

        $subject->delete();

        $this->assertSoftDeleted($subject);

        Livewire::test(Subjects::class)->call('destroySubject', $subject->id);

        $this->assertTrue(Subject::where('id', $subject->id)->withTrashed()->doesntExist());
        
    }

    
}
