<?php

namespace Tests\Feature;

use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentPersistentTest extends TestCase
{
    use RefreshDatabase, WithFaker;
    
    /** @group students */
    public function testAStudentCanBePersistedToTheDatabase()
    {
        $this->withoutExceptionHandling();

        $payload = Student::factory()->make()->toArray();

        $payload['level_id'] = $payload['admission_level_id'];

        $student = Student::create($payload);

        $this->assertEquals($payload['adm_no'], $student->adm_no);
        $this->assertEquals($payload['name'], $student->name);
        $this->assertEquals($payload['gender'], $student->gender);
        $this->assertEquals($payload['admission_level_id'], $student->admission_level_id);
        $this->assertEquals($payload['level_id'], $student->level_id);
    }
}
