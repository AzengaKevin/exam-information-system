<?php

namespace App\Repositories;

use App\Models\Subject;

class SubjectRepository{

    /**
     * Get all subjects that are optional from the database
     * 
     * @return Collection
     */
    public function findOptionalSubjects()
    {
        return Subject::optional()->get();
    }
}