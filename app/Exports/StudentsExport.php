<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection
{
    public array $filters;
    
    /**
     * Creates the exporter instance
     * 
     * @param array
     */
    public function __construct(array $filters = []) {
        $this->filters = $filters;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if(empty($this->filters)){

            $query = DB::table('students')->select(['adm_no','name','kcpe_marks','kcpe_grade','gender','dob', 'level_units.alias'])
                ->join('level_units', 'students.level_unit_id', '=', 'level_units.id');

            return $query->get(['adm_no', 'name', 'kcpe_marks', 'kcpe_grade', 'gender', 'dob', 'alias']);;
        }
    }
}
