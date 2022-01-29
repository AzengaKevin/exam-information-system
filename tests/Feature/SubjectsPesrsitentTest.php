<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubjectsPesrsitentTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group subjects */
    public function testASubjectCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Subject::factory()->make()->toArray();

        $subject = Subject::create($payload);

        $this->assertEquals($payload['name'], $subject->name);
        $this->assertEquals($payload['shortname'], $subject->shortname);
        $this->assertEquals($payload['description'], $subject->description);
        
    }

    /** @group subjects */
    public function testASubjectsWithSegmentsCanBePersisted()
    {
        $this->withoutExceptionHandling();

        /** @var Level */
        $level = Level::factory()->create(['numeric' => 8]);

        /** @var Level */
        $levelTwo = Level::factory()->create(['numeric' => 7]);

        $payload = Subject::factory()->make([
            'name' => 'English',
            'shortname' => 'eng',
            'segments' => [
                $level->id => [
                    'grammer' => 60,
                    'composition' => 40
                ],
                $levelTwo->id => [
                    'grammer' => 60,
                    'composition' => 40,
                    'literature' => 30
                ]
            ]
        ])->toArray();

        $subject = Subject::create($payload);

        $this->assertEquals($payload['name'], $subject->name);
        $this->assertEquals($payload['shortname'], $subject->shortname);
        $this->assertEquals($payload['description'], $subject->description);
        $this->assertEquals($payload['segments'], $subject->segments);
    }
}
