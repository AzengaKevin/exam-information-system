<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Livewire\Livewire;
use App\Models\Subject;
use App\Http\Livewire\Subjects;
use Livewire\Testing\TestableLivewire;
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

    /** @group subjects */
    public function testAuthorizedUsersCanAddASubject()
    {
        $this->withoutExceptionHandling();

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

        $payload = Subject::factory()->make([
            'name' => 'English',
            'shortname' => 'eng',
            'segments' => [
                ['key' => 'outOf60', 'value' => 60],
                ['key' => 'comp','value' => 40]
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
        $this->assertEquals(['outOf60' => 60, 'comp'=> 40], $subject->segments);
        
    }

    /** @group subjects */
    public function testAuthorizedUserCanTruncateSubjects()
    {
        $this->withExceptionHandling();

        Subject::factory(2)->create();

        Livewire::test(Subjects::class)
            ->call('truncateSubjects');

        $this->assertEquals(0, Subject::count());
        
    }

    
}
