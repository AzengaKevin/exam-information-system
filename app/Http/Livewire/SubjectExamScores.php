<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Grading;
use App\Models\Subject;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Livewire\WithPagination;

class SubjectExamScores extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Exam $exam;
    public Subject $subject;
    public $level;
    public $levelUnit;

    /**
     * Call when the subject-exam-scores compoent is mounting
     * 
     * @param Exam $exam
     * @param Subject $subject
     * @param mixed $level
     * @param mixed $levelUnit
     * 
     */
    public function mount(Exam $exam, Subject $subject, $level = null, $levelUnit = null)
    {
        $this->exam = $exam;
        $this->subject = $subject;
        $this->level = $level;
        $this->levelUnit = $levelUnit;
    }

    public function render()
    {
        return view('livewire.subject-exam-scores', [
            'data' => $this->getRelevantExamData()
        ]);
    }

    /**
     * Get the relevant data to show in the view
     * 
     * @return Paginator
     */
    public function getRelevantExamData()
    {
        $tblName = Str::slug($this->exam->shortname);

        $col = $this->subject->shortname;

        $query = DB::table('students')
            ->leftJoin("{$tblName}", "students.id", "=", "{$tblName}.student_id")
            ->select("students.id", "students.adm_no", "students.name", "{$tblName}.{$col}");
            // ->selectRaw("students.id, students.adm_no, students.name, `{$tblName}`.{$col}, CAST(JSON_UNQUOTE(JSON_EXTRACT(`{$tblName}`.$col,\"$.score\")) AS UNSIGNED) AS score");

        if ($this->level) $query->where('students.level_id', $this->level->id);

        if (!is_null($this->levelUnit)) $query->where('students.level_unit_id', $this->levelUnit->id);
            
        //return $query->orderBy('score', 'desc')->paginate(24);
        return $query->paginate(24);
    }

    /** 
     * Ranks the students that did the subject in question
     */
    public function rankSubjectResults()
    {
        try {
    
            $tblName = Str::slug($this->exam->shortname);
    
            $col = $this->subject->shortname;
            
            /** @var Collection */
            $query = DB::table($tblName)->selectRaw("student_id, CAST(JSON_UNQUOTE(JSON_EXTRACT($col,\"$.score\")) AS UNSIGNED) AS score");
    
            if(!is_null($this->level)) $query->where("level_id", $this->level->id);
    
            if(!is_null($this->levelUnit)) $query->where('level_unit_id', $this->levelUnit->id);
    
            /** @var Collection */
            $data = $query->orderBy("score", 'desc')->get();
    
            // Get the total records count
            $total = $data->count();
    
            $data->each(function($item, $key) use ($tblName, $col, $total){

                $rank = $key + 1;
    
                DB::update("UPDATE `$tblName` SET `$col` = JSON_SET(`$col`, \"$.rank\", $rank, \"$.total\", $total) WHERE student_id = {$item->student_id}");

            });
    
            session()->flash("status", "Students {$this->subject->name} rank successfully generated and stored");

            $this->emit('hide-generate-rank');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'A fatal db error occurred');

            $this->emit('hide-generate-rank');
        }
    }

    /**
     * Calculate and update total score for each subject
     */
    public function calculateTotalScore()
    {
        try {
    
            $tblName = Str::slug($this->exam->shortname);
    
            $col = $this->subject->shortname;

            $grading = Grading::first();

            $values = $grading->values;

            /** @var array */
            $segments = $this->subject->segments;

            $grandTotal = array_reduce(array_values($segments), fn($prevSum, $currItem) => intval($prevSum) + intval($currItem), 0);

            $query = DB::table($tblName)->select("student_id", "{$col}");
    
            if(!is_null($this->level)) $query->where("level_id", $this->level->id);
    
            if(!is_null($this->levelUnit)) $query->where('level_unit_id', $this->levelUnit->id);
    
            /** @var Collection */
            $data = $query->get();
            
            $data->each(function($studentData) use($grandTotal, $segments, $tblName, $col, $values){

                $score = json_decode($studentData->$col);

                $total = 0;

                foreach ($segments as $key => $value) {
                    $total += intval($score->$key);
                }

                $percentScore = (floatval($total)/$grandTotal) * 100.0;

                $percentScore = intval($percentScore);
                $grade = null;
                $points = null;

                foreach ($values as $value) {
                    if($percentScore >= $value['min'] && $percentScore <= $value['max']){
                        $grade = $value['grade'];
                        $points = $value['points'];
                        break;
                    }
                }

                DB::update("UPDATE `$tblName` SET `$col` = JSON_SET(`$col`, \"$.score\", {$percentScore}, \"$.grade\", '{$grade}', \"$.points\", {$points}) WHERE student_id = {$studentData->student_id}");
                
            });
            
            session()->flash("status", "Students {$this->subject->name} totals successfully generated and stored");

            $this->exam = $this->exam->fresh();

            $this->emit('hide-generate-totals');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', 'Subject totals calculation error');

            $this->emit('hide-generate-totals');
        }
        
    }
}
