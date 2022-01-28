<?php

namespace App\Services;

use App\Repositories\StudentRepository;

class StudentService
{
    private StudentRepository $studentRepository;

    public function __construct(StudentRepository $studentRepository) {
        $this->studentRepository = $studentRepository;
    }
    

    /**
     * Get a list of students with their primary guardians
     */
    public function getStudentsWithPrimaryGuardians(array $filters)
    {
        return $this->studentRepository->findStudentWithPrimaryGuardians($filters);
    }
}
