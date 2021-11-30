<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use App\Models\User;
use Livewire\Livewire;
use Illuminate\Support\Str;
use App\Http\Livewire\Exams;
use App\Http\Livewire\Levels;
use App\Models\Level;
use App\Models\Subject;
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

    /** @group exams */
    public function testAuthorizedUserCanCreateExam()
    {
        $this->withoutExceptionHandling();

        $payload = Exam::factory()->make()->toArray();
        

        Livewire::test(Exams::class)
            ->set('name', $payload['name'])
            ->set('shortname', $payload['shortname'])
            ->set('year', $payload['year'])
            ->set('term', $payload['term'])
            ->set('start_date', $payload['start_date'])
            ->set('end_date', $payload['end_date'])
            ->set('weight', $payload['weight'])
            ->set('counts', $payload['counts'])
            ->set('description', $payload['description'])
            ->call('createExam');

        $exam = Exam::first();

        $this->assertNotNull($exam);

        $this->assertEquals($payload['name'], $exam->name);
        $this->assertEquals($payload['term'], $exam->term);
        $this->assertEquals($payload['shortname'], $exam->shortname);
        $this->assertEquals(Str::slug($payload['shortname']), $exam->slug);
        $this->assertEquals($payload['year'], $exam->year);
        $this->assertEquals($payload['start_date'], $exam->start_date);
        $this->assertEquals($payload['end_date'], $exam->end_date);
        $this->assertEquals($payload['weight'], $exam->weight);
        $this->assertEquals($payload['counts'], $exam->counts);
        $this->assertEquals($payload['description'], $exam->description);
    }


    /** @group exams */
    public function testAuthorizedUserCanEnrollLevels()
    {
        $this->withoutExceptionHandling();

        $levelsIds = Level::factory(2)->create()->pluck('id')->toArray();

        /** @var Exam */
        $exam = Exam::factory()->create();

        $payload = array();

        foreach ($levelsIds as $id) {
            $payload[$id] = 'true';
        };

        Livewire::test(Exams::class)
            ->call('showEnrollLevelsModal', $exam)
            ->set('selectedLevels', $payload)
            ->call('updateExamLevels');
        
        $this->assertEquals(count($payload), $exam->levels()->count());
        
    }

    /** @group exams */
    public function testAuthorizedUsersCanEnrollSubjectsToExam()
    {
        $this->withoutExceptionHandling();

        /** @var Exam */
        $exam = Exam::factory()->create();

        $subjectsIds = Subject::factory(2)->create()->pluck('id')->toArray();

        $payload = array();

        foreach ($subjectsIds as $id) {
            $payload[$id] = 'true';
        }

        Livewire::test(Exams::class)
            ->call('showEnrollSubjectsModal', $exam)
            ->set('selectedSubjects', $payload)
            ->call('enrollSubjects');

        $this->assertEquals(count($payload), $exam->subjects()->count());
        
    }
}
