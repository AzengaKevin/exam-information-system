<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Repositories\StudentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StudentRepositoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @group students */
    public function testFindPaginatedMethod()
    {
        $this->withoutExceptionHandling();
        
        Student::factory()->count($studentCount = 3)->create();

        $studentRepository = new StudentRepository();

        $students = $studentRepository->findPaginated();

        $this->assertEquals($studentCount, $students->total());
    }

    /** @group students */
    public function testFindPaginatedMethodWhenSomeStudentsAreArchived()
    {
        $this->withoutExceptionHandling();
        
        Student::factory()->count($studentCount = 3)->create();

        $randStudent = Student::inRandomOrder()->first();

        $randStudent->update(["archived_at" => now()]);

        $studentRepository = new StudentRepository();

        $students = $studentRepository->findPaginated();

        $this->assertEquals(($studentCount - 1), $students->total());
        
    }
}
