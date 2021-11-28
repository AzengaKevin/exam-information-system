<?php

namespace Tests\Feature;

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
}
