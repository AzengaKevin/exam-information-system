<?php

namespace App\Repositories;

use Illuminate\Support\Facades\DB;

class StudentRepository
{

    /**
     * Gets a collection of students with their primary guardians details
     * 
     * @param array $filters - Group of students to get
     * @return Collection
     */
    public function findStudentWithPrimaryGuardians(array $filters)
    {
        $studentsQuery = DB::table('students')
            ->select(["students.id AS student_id", "students.name AS student_name"])->addSelect([
                "guardian_name" => DB::table('student_guardians')
                    ->join('users', function ($join) {
                        $join->on('student_guardians.guardian_id', '=', 'users.authenticatable_id')
                            ->where('users.authenticatable_type', 'guardian');
                    })
                    ->select("users.name")
                    ->whereColumn('students.id', '=', 'student_guardians.student_id')
                    ->take('1'),

                "guardian_phone" => DB::table('student_guardians')
                    ->join('users', function ($join) {
                        $join->on('student_guardians.guardian_id', '=', 'users.authenticatable_id')
                            ->where('users.authenticatable_type', 'guardian');
                    })
                    ->select("users.phone")
                    ->whereColumn('students.id', '=', 'student_guardians.student_id')
                    ->take('1'),

                "guardian_email" => DB::table('student_guardians')
                    ->join('users', function ($join) {
                        $join->on('student_guardians.guardian_id', '=', 'users.authenticatable_id')
                            ->where('users.authenticatable_type', 'guardian');
                    })
                    ->select("users.email")
                    ->whereColumn('students.id', '=', 'student_guardians.student_id')
                    ->take('1'),

                "location" => DB::table('student_guardians')
                    ->join('guardians', 'student_guardians.guardian_id', '=', 'guardians.id')
                    ->select("location")
                    ->whereColumn('students.id', '=', 'student_guardians.student_id')
                    ->take('1'),

                "profession" => DB::table('student_guardians')
                    ->join('guardians', 'student_guardians.guardian_id', '=', 'guardians.id')
                    ->select("profession")
                    ->whereColumn('students.id', '=', 'student_guardians.student_id')
                    ->take('1')
            ]);

        // Filter with level_id if exists
        if(array_key_exists('level_id', $filters) && !empty($filters['level_id'])) 
            $studentsQuery->where('level_id', $filters['level_id']);

        // Filter with level_unit_id if exists
        if(array_key_exists('level_unit_id', $filters) && !empty($filters['level_unit_id'])) 
            $studentsQuery->where('level_unit_id', $filters['level_unit_id']);

        return $studentsQuery->get();

    }
    
}
