<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Student;
use App\Models\Subject;
use App\Models\LevelUnit;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ExamsTranscriptsController extends Controller
{

    /**
     * Show all the level units that took the exam and a button to navigating to the transcripts page
     * 
     * @param Request $request
     * @param Exam $exam
     * 
     */
    public function index(Request $request, Exam $exam)
    {
        $levelsIds = $exam->levels->pluck('id')->toArray();

        $levelUnits = LevelUnit::whereIn('level_id', $levelsIds)->get();

        return view('exams.transcripts.index', compact('levelUnits', 'exam'));
    }

    /**
     * Show transcripts for all students in the specified exam on the same page
     *  
     * @param Request $request
     * @param Exam $exam
     */
    public function show(Request $request, Exam $exam)
    {
        $levelUnitId = $request->get('level-unit');

        try {

            $outOfs = array();

            $levelUnit = LevelUnit::findOrFail($levelUnitId);

            // Get the student results
            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColumns = $exam->subjects->pluck("shortname")->toArray();

            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();


            $teachers = DB::table('responsibility_teacher')
                ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                ->join('users', function($join){
                    $join->on('teachers.id', '=', 'users.authenticatable_id')
                        ->where('users.authenticatable_type', 'teacher');
                })->select('users.name', 'subjects.shortname')
                    ->where('responsibility_teacher.level_unit_id', $levelUnit->id)
                    ->get()->pluck('name', 'shortname')->toArray();



            $outOfs["lsc"] = DB::table($examScoresTblName)
                ->select("admno")
                ->distinct("admno")
                ->where('level_id', $levelUnit->level_id)
                ->count();

            $outOfs["lusc"] = DB::table($examScoresTblName)
                ->select("admno")
                ->distinct("admno")
                ->where('level_unit_id', $levelUnit->id)
                ->count();


            /** @var Collection */
            $studentsScores = DB::table($examScoresTblName)
                ->select(array_merge($subjectColumns, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.admno", "=", "students.adm_no")
                ->join("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->join("hostels", "students.hostel_id", "=", "hostels.id", 'left')
                ->where("{$examScoresTblName}.level_unit_id", $levelUnit->id)
                ->get();                
                
            $subjectsCount = 0;
        
            foreach ($subjectColumns as $col) {
                if(!empty($studentsScores->first()->$col)){
                    $subjectsCount++;
                }
            }

            $outOfs["tm"] = $subjectsCount * 100;
            $outOfs["tp"] = $subjectsCount * 12;
            $outOfs["mm"] = 100;
            $outOfs["mg"] = 'A';
            
            return view('exams.transcripts.show', [
                'exam' => $exam,
                'levelUnit' => $levelUnit,
                'studentsScores' => $studentsScores,
                'subjectColumns' => $subjectColumns,
                'subjectsMap' => $subjectsMap,
                'swahiliComments' => $swahiliComments,
                'englishComments' => $englishComments,
                'ctComments' => $ctComments,
                'pComments' => $pComments,
                'teachers' => $teachers,
                'outOfs' => $outOfs
            ]);

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            abort(404, 'You tried playing tricks, don\'t');
        }

    }

    public function studentShow(Request $request, Exam $exam)
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

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();

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
            'ctComments' => $ctComments,
            'pComments' => $pComments,
            'teachers' => $teachers ?? [],
            'outOfs' => $outOfs
        ]);
    }

    /**
     * Print a single transcript for a single student specified by admission number
     * 
     * @param Request $request
     * @param Exam $exam
     */
    public function printOne(Request $request, Exam $exam)
    {

        $admno = $request->get('admno');

        try {

            // Get subject teachers
            $student = Student::where('adm_no', $admno)->firstOrFail();

            $outOfs = array();
            
            $examScoresTblName = Str::slug($exam->shortname);
    
            $subjectColums = $exam->subjects->pluck("shortname")->toArray();
    
            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');
    
            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();

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
                'studentScores' => $studentScores,
                'subjectColums' => $subjectColums,
                'subjectsMap' => $subjectsMap,
                'swahiliComments' => $swahiliComments,
                'englishComments' => $englishComments,
                'ctComments' => $ctComments,
                'pComments' => $pComments,
                'teachers' => $teachers,
                'outOfs' => $outOfs
            ]);
    
            return $pdf->download("{$exam->shortname}-{$admno}.pdf");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'admno' => $admno,
                'action' => __METHOD__
            ]);

            abort(404, 'You tried playing tricks, don\'t');
            
        }

    }

    /**
     * Print all transcripts for the students in a specific class
     * 
     * @param Request $request
     * @param Exam $exam
     * 
     */
    public function printBulk(Request $request, Exam $exam)
    {
        $levelUnitId = $request->get('level-unit');

        try {

            $outOfs = array();

            $levelUnit = LevelUnit::findOrFail($levelUnitId);

            // Get the student results
            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColumns = $exam->subjects->pluck("shortname")->toArray();

            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();


            $teachers = DB::table('responsibility_teacher')
                ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                ->join('users', function($join){
                    $join->on('teachers.id', '=', 'users.authenticatable_id')
                        ->where('users.authenticatable_type', 'teacher');
                })->select('users.name', 'subjects.shortname')
                    ->where('responsibility_teacher.level_unit_id', $levelUnit->id)
                    ->get()->pluck('name', 'shortname')->toArray();


            $outOfs["lsc"] = DB::table($examScoresTblName)
                ->select("admno")
                ->distinct("admno")
                ->where('level_id', $levelUnit->level_id)
                ->count();

            $outOfs["lusc"] = DB::table($examScoresTblName)
                ->select("admno")
                ->distinct("admno")
                ->where('level_unit_id', $levelUnit->id)
                ->count();


            /** @var Collection */
            $studentsScores = DB::table($examScoresTblName)
                ->select(array_merge($subjectColumns, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.admno", "=", "students.adm_no")
                ->join("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->join("hostels", "students.hostel_id", "=", "hostels.id", 'left')
                ->where("{$examScoresTblName}.level_unit_id", $levelUnit->id)
                ->get();                
                
            $subjectsCount = 0;
        
            foreach ($subjectColumns as $col) {
                if(!empty($studentsScores->first()->$col)){
                    $subjectsCount++;
                }
            }

            $outOfs["tm"] = $subjectsCount * 100;
            $outOfs["tp"] = $subjectsCount * 12;
            $outOfs["mm"] = 100;
            $outOfs["mg"] = 'A';

            $pdf = \PDF::loadView("printouts.exams.transcripts", [
                'exam' => $exam,
                'levelUnit' => $levelUnit,
                'studentsScores' => $studentsScores,
                'subjectColumns' => $subjectColumns,
                'subjectsMap' => $subjectsMap,
                'swahiliComments' => $swahiliComments,
                'englishComments' => $englishComments,
                'ctComments' => $ctComments,
                'pComments' => $pComments,
                'teachers' => $teachers,
                'outOfs' => $outOfs
            ]);
    
            return $pdf->download("{$exam->shortname}-{$levelUnit->alias}.pdf");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            abort(404, 'You tried playing tricks, don\'t');
        }
    }
    
}
