<?php

namespace App\Imports;

use App\Models\LevelUnit;
use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class StudentsImport implements ToModel, WithStartRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        /** @var LevelUnit */
        $levelUnit = LevelUnit::where('alias', $row[6])->first();

        return Student::create([
            'adm_no' => $row[0],
            'name' => $row[1],
            'kcpe_marks' => $row[2],
            'kcpe_grade' => $row[3],
            'gender' => $row[4],
            'dob' => $row[5],
            'level_id' => $levelUnit->level->id,
            'stream_id' => $levelUnit->stream->id,
            'level_unit_id' => $levelUnit->id
        ]);
    }

    public function startRow(): int
    {
        return 2;
    }
}
