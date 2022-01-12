<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Student;
use App\Models\Subject;
use App\Models\LevelUnit;
use App\Models\Responsibility;
use App\Settings\GeneralSettings;
use App\Settings\SystemSettings;
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
        $this->authorize('viewTranscripts', $exam);

        $levelsIds = $exam->levels->pluck('id')->toArray();

        $levelUnits = LevelUnit::whereIn('level_id', $levelsIds)->get();

        return view('exams.transcripts.index', compact('levelUnits', 'exam'));
    }

    /**
     * Show transcripts for all students in the specified exam on the same page
     *  
     * @param Request $request
     * @param Exam $exam
     * @param SystemSettings $systemSettings
     * @param GeneralSettings $generalSettings
     */
    public function show(Request $request, Exam $exam, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        $levelUnitId = $request->get('level-unit');

        $levelId = $request->get('level');

        try {

            $outOfs = array();

            /** @var LevelUnit */
            $levelUnit = LevelUnit::find($levelUnitId);

            /** @var Level */
            $level = Level::find($levelId);

            // Get the student results
            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColumns = $exam->subjects->pluck("shortname")->toArray();

            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();


            $teachersQuery = DB::table('responsibility_teacher')
                ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                ->join('users', function($join){
                    $join->on('teachers.id', '=', 'users.authenticatable_id')
                        ->where('users.authenticatable_type', 'teacher');
                })->select('users.name', 'subjects.shortname');

            if ($levelUnit) $teachersQuery->where('responsibility_teacher.level_unit_id', $levelUnit->id);
            if ($level) $teachersQuery->where('responsibility_teacher.level_id', $level->id);
                    
            $teachers = $teachersQuery->get()->pluck('name', 'shortname')->toArray();

            /** @var Responsibility */
            $pRes = Responsibility::firstOrCreate(['name' => 'Principal']);
            $teachers['p'] = optional(optional($pRes->teachers()->latest()->first())->auth)->name;

            /** @var Responsibility */
            $ctRes = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

            if ($levelUnit) $teachers['ct'] = optional(optional($ctRes->teachers()->wherePivot('level_unit_id', $levelUnit->id)->first())->auth)->name;
            if ($level) $teachers['ct'] = optional(optional($ctRes->teachers()->wherePivot('level_id', $level->id)->first())->auth)->name;

            if ($levelUnit) {
                
                $outOfs["lsc"] = DB::table($examScoresTblName)
                    ->select("student_id")
                    ->distinct("student_id")
                    ->where('level_id', $levelUnit->level_id)
                    ->count();
    
                $outOfs["lusc"] = DB::table($examScoresTblName)
                    ->select("student_id")
                    ->distinct("student_id")
                    ->where('level_unit_id', $levelUnit->id)
                    ->count();
            }

            if($level){
                
                $outOfs["lsc"] = DB::table($examScoresTblName)
                    ->select("student_id")
                    ->distinct("student_id")
                    ->where('level_id', $level->id)
                    ->count();
            }
            
            $studentsScoresQuery = DB::table($examScoresTblName)
                ->select(array_merge($subjectColumns, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "students.id AS student_id", "level_units.alias", "levels.name AS level", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.student_id", "=", "students.id")
                ->leftJoin("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->leftJoin("levels", "{$examScoresTblName}.level_id", "=", "levels.id")
                ->leftJoin("hostels", "students.hostel_id", "=", "hostels.id");
            
            if ($levelUnit) $studentsScoresQuery->where("{$examScoresTblName}.level_unit_id", $levelUnit->id);
            if ($level) $studentsScoresQuery->where("{$examScoresTblName}.level_id", $level->id);

            /** @var Collection */
            $studentsScores = $studentsScoresQuery->get();                
                
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

            $title = "Transcripts";

            if ($levelUnit) $title = "{$exam->name} - {$levelUnit->alias} - Transcripts";
            if ($level) $title = "{$exam->name} - {$level->name} - Transcripts";
            
            return view('exams.transcripts.show', [
                'exam' => $exam,
                'levelUnit' => $levelUnit,
                'level' => $level,
                'studentsScores' => $studentsScores,
                'subjectColumns' => $subjectColumns,
                'subjectsMap' => $subjectsMap,
                'swahiliComments' => $swahiliComments,
                'englishComments' => $englishComments,
                'ctComments' => $ctComments,
                'pComments' => $pComments,
                'teachers' => $teachers,
                'outOfs' => $outOfs,
                'title' => $title,
                'systemSettings' => $systemSettings,
                'generalSettings' => $generalSettings
            ]);

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            abort(404, 'You tried playing tricks, don\'t');
        }

    }

    /**
     * Show a page with a asingle student transcript
     * 
     * @param Request $request
     * 
     * @param Exam $exam
     */
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
                ->leftJoin("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->leftJoin("hostels", "students.hostel_id", "=", "hostels.id")
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
     * @param SystemSettings $systemSettings
     * @param GeneralSettings $generalSettings
     */
    public function printOne(Request $request, Exam $exam, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {

        $id = $request->get('id');

        try {

            /** @var Student */
            $student = Student::findOrFail($id);

            $outOfs = array();
            
            $examScoresTblName = Str::slug($exam->shortname);
    
            $subjectColumns = $exam->subjects->pluck("shortname")->toArray();
    
            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');
    
            $aggregateColumns = array("mm", "tm", "mg", "mp", "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();

            if ($student) {

                // Fetch the subject teachers appropriately
                $teachersQuery = DB::table('responsibility_teacher')
                    ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                    ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                    ->join('users', function($join){
                        $join->on('teachers.id', '=', 'users.authenticatable_id')
                                ->where('users.authenticatable_type', 'teacher');
                    })->select('users.name', 'subjects.shortname');

                if($systemSettings->school_has_streams) $teachersQuery->where('responsibility_teacher.level_unit_id', $student->level_unit_id);

                else $teachersQuery->where('responsibility_teacher.level_id', $student->level_id);

                $teachers = $teachersQuery->get()->pluck('name', 'shortname')->toArray();

                /** @var Responsibility */
                $pRes = Responsibility::firstOrCreate(['name' => 'Principal']);
                $teachers['p'] = optional(optional($pRes->teachers()->latest()->first())->auth)->name;

                /** @var Responsibility */
                $ctRes = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

                if($systemSettings->school_has_streams) $teachers['ct'] = optional(optional($ctRes->teachers()->wherePivot('level_unit_id', $student->level_unit_id)->first())->auth)->name;                        
                else $teachers['ct'] = optional(optional($ctRes->teachers()->wherePivot('level_id', $student->level_id)->first())->auth)->name;                        

                if ($systemSettings->school_has_streams) {
                    
                    $outOfs["lsc"] = DB::table($examScoresTblName)
                        ->select("student_id")
                        ->distinct("student_id")
                        ->where('level_id', $student->level_id)
                        ->count();
    
                    $outOfs["lusc"] = DB::table($examScoresTblName)
                        ->select("student_id")
                        ->distinct("student_id")
                        ->where('level_unit_id', $student->level_unit_id)
                        ->count();
                }else{
                    
                    $outOfs["lsc"] = DB::table($examScoresTblName)
                        ->select("student_id")
                        ->distinct("student_id")
                        ->where('level_id', $student->level_id)
                        ->count();
                }
            }
            
            $studentScores = DB::table($examScoresTblName)
                ->select(array_merge($subjectColumns, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "levels.name AS level", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.student_id", "=", "students.id")
                ->leftJoin("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->leftJoin("levels", "{$examScoresTblName}.level_id", "=", "levels.id")
                ->leftJoin("hostels", "students.hostel_id", "=", "hostels.id")
                ->where("{$examScoresTblName}.student_id", $student->id)
                ->first();

            $subjectsCount = 0;
            
            foreach ($subjectColumns as $col) {
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
                'subjectColumns' => $subjectColumns,
                'subjectsMap' => $subjectsMap,
                'swahiliComments' => $swahiliComments,
                'englishComments' => $englishComments,
                'ctComments' => $ctComments,
                'pComments' => $pComments,
                'teachers' => $teachers,
                'outOfs' => $outOfs,
                'systemSettings' => $systemSettings,
                'generalSettings' => $generalSettings
            ]);
    
            return $pdf->download("{$exam->shortname}-{$id}.pdf");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
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
     * @param SystemSettings $systemSettings
     * @param GeneralSettings $generalSettings
     * 
     */
    public function printBulk(Request $request, Exam $exam, SystemSettings $systemSettings, GeneralSettings $generalSettings)
    {
        $levelUnitId = $request->get('level-unit');

        $levelId = $request->get('level');

        try {

            $outOfs = array();

            $levelUnit = LevelUnit::find($levelUnitId);

            $level = Level::find($levelId);

            $examScoresTblName = Str::slug($exam->shortname);

            $subjectColumns = $exam->subjects->pluck("shortname")->toArray();

            $subjectsMap = Subject::all(['name', 'shortname'])->pluck('name', 'shortname');

            $aggregateColumns = array("mm", "tm", "mg", "mp",  "tp", "sp", "op");

            $swahiliComments = Grade::all(['grade', 'swahili_comment'])->pluck('swahili_comment', 'grade')->toArray();

            $englishComments = Grade::all(['grade', 'english_comment'])->pluck('english_comment', 'grade')->toArray();

            $ctComments = Grade::all(['grade', 'ct_comment'])->pluck('ct_comment', 'grade')->toArray();
            
            $pComments = Grade::all(['grade', 'p_comment'])->pluck('p_comment', 'grade')->toArray();

            $teachersQuery = DB::table('responsibility_teacher')
                ->join('subjects', 'responsibility_teacher.subject_id', '=', 'subjects.id')
                ->join('teachers', 'responsibility_teacher.teacher_id', '=', 'teachers.id')
                ->join('users', function($join){
                    $join->on('teachers.id', '=', 'users.authenticatable_id')
                        ->where('users.authenticatable_type', 'teacher');
                })->select('users.name', 'subjects.shortname');

            if($level) $teachersQuery->where('responsibility_teacher.level_id', $level->id);

            if($levelUnit) $teachersQuery->where('responsibility_teacher.level_unit_id', $levelUnit->id);

            $teachers = $teachersQuery->get()->pluck('name', 'shortname')->toArray();

            /** @var Responsibility */
            $pRes = Responsibility::firstOrCreate(['name' => 'Principal']);
            $teachers['p'] = optional(optional($pRes->teachers()->latest()->first())->auth)->name;
            
            /** @var Responsibility */
            $ctRes = Responsibility::firstOrCreate(['name' => 'Class Teacher']);

            if($level) $teachers['ct'] = optional(optional($ctRes->teachers()->wherePivot('level_id', $level->id)->first())->auth)->name;
            
            if($levelUnit) $teachers['ct'] = optional(optional($ctRes->teachers()->wherePivot('level_unit_id', $levelUnit->id)->first())->auth)->name;

            if ($levelUnit) {
                
                $outOfs["lsc"] = DB::table($examScoresTblName)
                    ->select("student_id")
                    ->distinct("student_id")
                    ->where('level_id', $levelUnit->level_id)
                    ->count();
    
                $outOfs["lusc"] = DB::table($examScoresTblName)
                    ->select("student_id")
                    ->distinct("student_id")
                    ->where('level_unit_id', $levelUnit->id)
                    ->count();
            }

            if ($level) {

                $outOfs["lsc"] = DB::table($examScoresTblName)
                    ->select("student_id")
                    ->distinct("student_id")
                    ->where('level_id', $level->id)
                    ->count();
            }

            $studentsScoresQuery = DB::table($examScoresTblName)
                ->select(array_merge($subjectColumns, $aggregateColumns))
                ->addSelect(["students.name", "students.adm_no", "level_units.alias", "levels.name AS level", "hostels.name AS hostel"])
                ->join("students", "{$examScoresTblName}.student_id", "=", "students.id")
                ->leftJoin("level_units", "{$examScoresTblName}.level_unit_id", "=", "level_units.id")
                ->leftJoin("levels", "{$examScoresTblName}.level_id", "=", "levels.id")
                ->leftJoin("hostels", "students.hostel_id", "=", "hostels.id");

            if ($level) $studentsScoresQuery->where("{$examScoresTblName}.level_id", $level->id);

            if ($levelUnit) $studentsScoresQuery->where("{$examScoresTblName}.level_unit_id", $levelUnit->id);
            
            $studentsScores = $studentsScoresQuery->get();             
                
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

            
            $title = "Transcripts";
            
            if ($levelUnit) $title = "{$exam->name} - {$levelUnit->alias} - Transcripts";
            if ($level) $title = "{$exam->name} - {$level->name} - Transcripts";

            $pdf = \PDF::loadView("printouts.exams.transcripts", [
                'exam' => $exam,
                'levelUnit' => $levelUnit,
                'level' => $level,
                'studentsScores' => $studentsScores,
                'subjectColumns' => $subjectColumns,
                'subjectsMap' => $subjectsMap,
                'swahiliComments' => $swahiliComments,
                'englishComments' => $englishComments,
                'ctComments' => $ctComments,
                'pComments' => $pComments,
                'teachers' => $teachers,
                'outOfs' => $outOfs,
                'title' => $title,
                'systemSettings' => $systemSettings,
                'generalSettings' => $generalSettings
            ]);

            $name = "transacripts";

            if($level) $name = Str::slug($exam->shortname) . '-' . Str::slug($level->name);
            if($levelUnit) $name = Str::slug($exam->shortname) . '-'. Str::slug($levelUnit->alias);
    
            return $pdf->download("{$name}.pdf");

        } catch (\Exception $exception) {
            
            Log::error($exception->getMessage(), [
                'exam-id' => $exam->id,
                'action' => __METHOD__
            ]);

            abort(404, 'You tried playing tricks, don\'t');
        }
    }
    
}
