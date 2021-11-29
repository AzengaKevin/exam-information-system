<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Exam;
use Illuminate\Support\Str;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExamPersistenceTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group exams */
    public function testAnExamCanBePersistedToTheDatabase()
    {
        $payload = Exam::factory()->make()->toArray();

        $exam = Exam::create($payload);

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
}
