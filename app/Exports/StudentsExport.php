<?php

namespace App\Exports;

use App\Settings\SystemSettings;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
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
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $studentQuery = DB::table('students')->orderBy('students.name')
            ->leftJoin('levels', 'students.level_id', '=', 'levels.id')
            ->leftJoin('streams', 'students.stream_id', '=', 'streams.id');

        if($systemSettings->school_level === 'secondary'){
            $studentQuery->select('students.name','students.adm_no','levels.numeric AS level','streams.alias AS stream', 'gender', 'dob', 'upi', 'kcpe_marks', 'kcpe_grade');
        }else{
            $studentQuery->select('students.name','students.adm_no','levels.numeric AS level','streams.alias AS stream', 'gender', 'dob', 'upi');
        }

        return $studentQuery->get();
    }

    /**
     * The headings for the exported students excel file
     * 
     * @return array
     */
    public function headings(): array
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $cols = ["NAME", "ADMNO", "LEVEL", "STREAM", "GENDER","DOB", "UPI", "KCPEMARKS", "KCPEGRADE"];

        if($systemSettings->school_level == 'primary')
            $cols = ["NAME", "ADMNO", "LEVEL", "STREAM", "GENDER", "DOB", "UPI"];

        return $cols;
    }

}
