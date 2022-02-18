<?php

namespace App\Repositories;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;

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

    /**
     * Get all subjects that are compulsory
     * 
     * @return Collection
     */
    public function findCompulsorySubjects()
    {
        return Subject::compulsory()->get();
    }

    /**
     * Get all filtered optional subjects
     * 
     * @return Collection
     */
    public function findFilteredOptionalSubjects(array $filters)
    {
        $subjectsQuery = Subject::optional();

        if(array_key_exists('level_id', $filters) && $filters['level_id']){
            $subjectsQuery->whereExists(function($query) use($filters){
                $query->select(DB::raw(1))
                    ->from('level_subject')
                    ->whereColumn('level_subject.subject_id', 'subjects.id')
                    ->where('level_subject.level_id', $filters['level_id']);
            });
        }

        if(array_key_exists('stream_id', $filters)){
            $subjectsQuery->whereExists(function($query) use($filters){
                $query->select(DB::raw(1))
                    ->from('stream_subject')
                    ->whereColumn('stream_subject.subject_id', 'subjects.id')
                    ->where('stream_subject.stream_id', $filters['stream_id']);
            });
        }

        return $subjectsQuery->get();
    }
}