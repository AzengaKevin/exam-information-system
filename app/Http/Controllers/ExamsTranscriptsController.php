<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
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

        $outOfs = array();

        if($admno){

            // Get the student results
            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColums = $exam->subjects->pluck("shortname")->toArray();

            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            // Get subject teachers
            $student = Student::where('adm_no', $admno)->first();

            if ($student) {

                $teachers = DB::table('responsibility_teacher')
                    ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                    ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                    ->join('users', function($join){
                        $join->on('teachers.id', '=', 'users.authenticatable_id')
                             ->where('users.authenticatable_type', 'teacher');
                    })->select('users.name', 'subjects.shortname')
                        ->where('responsibility_teacher.level_unit_id', $student->level_unit_id)
                        ->get()->pluck('name', 'shortname')->toArray();

                $outOfs["lsc"] = DB::table($examScoresTblName)
                    ->select("admno")
                    ->distinct("admno")
                    ->where('level_id', $student->level_id)
                    ->count();

                $outOfs["lusc"] = DB::table($examScoresTblName)
                    ->select("admno")
                    ->distinct("admno")
                    ->where('level_unit_id', $student->level_unit_id)
                    ->count();

            }
            
            $studentScores = DB::table($examScoresTblName)
                ->select(array_merge($subjectColums, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.admno", "=", "students.adm_no")
                ->join("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->join("hostels", "students.hostel_id", "=", "hostels.id", 'left')
                ->where("{$examScoresTblName}.admno", $admno)
                ->first();

            $subjectsCount = 0;
            
            foreach ($subjectColums as $col) {
                if(!empty($studentScores->$col)){
                    $subjectsCount++;
                }
            }

            $outOfs["tm"] = $subjectsCount * 100;
            $outOfs["tp"] = $subjectsCount * 12;
            $outOfs["mm"] = 100;
            $outOfs["mg"] = 'A';

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
            'subjectsMap' => $subjectsMap ?? [],
            'swahiliComments' => $swahiliComments ?? [],
            'englishComments' => $englishComments ?? [],
            'teachers' => $teachers ?? [],
            'outOfs' => $outOfs
        ]);
    }

    public function print(Request $request, Exam $exam)
    {

        $admno = $request->get('admno');

        if ($admno) {

            $outOfs = array();
            
            $examScoresTblName = Str::slug($exam->shortname);
    
            $subjectColums = $exam->subjects->pluck("shortname")->toArray();
    
            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');
    
            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            // Get subject teachers
            $student = Student::where('adm_no', $admno)->first();

            if ($student) {

                $teachers = DB::table('responsibility_teacher')
                    ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                    ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                    ->join('users', function($join){
                        $join->on('teachers.id', '=', 'users.authenticatable_id')
                                ->where('users.authenticatable_type', 'teacher');
                    })->select('users.name', 'subjects.shortname')
                        ->where('responsibility_teacher.level_unit_id', $student->level_unit_id)
                        ->get()->pluck('name', 'shortname')->toArray();

                $outOfs["lsc"] = DB::table($examScoresTblName)
                    ->select("admno")
                    ->distinct("admno")
                    ->where('level_id', $student->level_id)
                    ->count();

                $outOfs["lusc"] = DB::table($examScoresTblName)
                    ->select("admno")
                    ->distinct("admno")
                    ->where('level_unit_id', $student->level_unit_id)
                    ->count();
            }
            
            $studentScores = DB::table($examScoresTblName)
                ->select(array_merge($subjectColums, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.admno", "=", "students.adm_no")
                ->join("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->join("hostels", "students.hostel_id", "=", "hostels.id", 'left')
                ->where("{$examScoresTblName}.admno", $admno)
                ->first();

            $subjectsCount = 0;
            
            foreach ($subjectColums as $col) {
                if(!empty($studentScores->$col)){
                    $subjectsCount++;
                }
            }

            $outOfs["tm"] = $subjectsCount * 100;
            $outOfs["tp"] = $subjectsCount * 12;
            $outOfs["mm"] = 100;
            $outOfs["mg"] = 'A';
    
            $pdf = \PDF::loadView("printouts.exams.report-form",  [
                'exam' => $exam,
                'studentScores' => $studentScores ?? null,
                'subjectColums' => $subjectColums ?? [],
                'subjectsMap' => $subjectsMap ?? [],
                'swahiliComments' => $swahiliComments ?? [],
                'englishComments' => $englishComments ?? [],
                'teachers' => $teachers ?? [],
                'outOfs' => $outOfs
            ]);
    
            return $pdf->download("{$exam->shortname}-{$admno}.pdf");
        }else{
            return back();
        }

    }
    
}
