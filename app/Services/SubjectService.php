<?php

namespace App\Services;

use App\Repositories\SubjectRepository;

class SubjectService
{
    private SubjectRepository $subjectRepository;

    public function __construct(SubjectRepository $subjectRepository) {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * Get a collection of optional subjects from the database
     */
    public function getOptionalSubjects()
    {
        return $this->subjectRepository->findOptionalSubjects();
        
    }
}
