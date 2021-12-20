<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Student;
use App\Models\LevelUnit;
use App\Models\Subject;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExamsTranscriptsController extends Controller
{

    public function index(Request $request, Exam $exam)
    {

        $admno = $request->get('admno');

        /** @var LevelUnit */
        $levelUnit = LevelUnit::find(intval($request->get('level-unit')));

        /** @var Level */
        $level = Level::find(intval($request->get('level')));

        if($admno){

            // Get the student results
            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColums = $exam->subjects->pluck("shortname")->toArray();

            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");
            
            $studentScores = DB::table($examScoresTblName)
                ->select(array_merge($subjectColums, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.admno", "=", "students.adm_no")
                ->join("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->join("hostels", "students.hostel_id", "=", "hostels.id", 'left')
                ->where("{$examScoresTblName}.admno", $admno)
                ->first();

        }else{
            
            //Get the appropriate students to show transacripts
            
            $studentsQuery = Student::whereIn('level_id', $exam->levels->pluck('id')->toArray())
                ->orderBy('adm_no');
    
            if ($level) {
                $studentsQuery->where('level_id', $level->id);
            }
    
            if($levelUnit){
                $studentsQuery->where('level_unit_id', $levelUnit->id);
            }
    
            $students = $studentsQuery->paginate(24);

        }

        return view('exams.transcripts.index', [
            'exam' => $exam,
            'students' => $students ?? null,
            'levelUnit' => $levelUnit ?? null,
            'level' => $level ?? null,
            'studentScores' => $studentScores ?? null,
            'subjectColums' => $subjectColums ?? [],
            'subjectsMap' => $subjectsMap ?? []
        ]);
    }

    public function print(Request $request, Exam $exam)
    {

        $admno = $request->get('admno');

        $examScoresTblName = Str::slug($exam->shortname);

        $subjectColums = $exam->subjects->pluck("shortname")->toArray();

        $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

        $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");
        
        $studentScores = DB::table($examScoresTblName)
            ->select(array_merge($subjectColums, $aggregateColumns))
            ->addSelect(["students.name", "students.adm_no", "level_units.alias", "hostels.name AS hostel"])
            ->join("students", "{$examScoresTblName}.admno", "=", "students.adm_no")
            ->join("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
            ->join("hostels", "students.hostel_id", "=", "hostels.id", 'left')
            ->where("{$examScoresTblName}.admno", $admno)
            ->first();

        $pdf = \PDF::loadView("printouts.exams.report-form",  [
            'exam' => $exam,
            'studentScores' => $studentScores ?? null,
            'subjectColums' => $subjectColums ?? [],
            'subjectsMap' => $subjectsMap ?? []
        ]);

        return $pdf->download("{$exam->shortname}-{$admno}.pdf");
    }
    
}
