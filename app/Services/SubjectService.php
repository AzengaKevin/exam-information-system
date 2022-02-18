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
     * 
     * @return Collection
     */
    public function getOptionalSubjects()
    {
        return $this->subjectRepository->findOptionalSubjects();
    }

    /**
     * Gets a collection of all the compulsory subjects
     * 
     * @return Collection
     */
    public function getCompulsorySubjects()
    {
        return $this->subjectRepository->findCompulsorySubjects();
    }

    /**
     * Request filtered optional subjects from the database
     * 
     * @param array $filters
     * @return Collection
     */
    public function getFilteredOptionalSubjects(array $filters = [])
    {
        return $this->subjectRepository->findFilteredOptionalSubjects($filters);
        
    }
}
