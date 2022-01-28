<?php

namespace App\Imports;

use App\Models\Level;
use App\Models\LevelUnit;
use App\Models\Stream;
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
        /** @var Level */
        $level = Level::where('numeric', $row[2])->first();

        /** @var Stream */
        $stream = Stream::where('alias', $row[3])->first();

        /** @var LevelUnit */
        $levelUnit = LevelUnit::where([
            ['level_id', $level->id],
            ['stream_id', $stream->id],
        ])->first();

        return Student::create([
            'name' => $row[0],
            'adm_no' => $row[1],
            'gender' => $row[4],
            'dob' => $row[5],
            'level_id' => optional($level)->id,
            'stream_id' => optional($stream)->id,
            'level_unit_id' => optional($levelUnit)->id,
            'upi' => $row[6],
            'kcpe_marks' => $row[7] ?? null,
            'kcpe_grade' => $row[8] ?? null,
        ]);
    }

    /**
     * The students import start from the defined row
     * 
     * {@inheritdoc}
     */
    public function startRow(): int
    {
        return 2;
    }
}
