<?php

namespace App\Http\Livewire;

use App\Actions\Exam\Scores\CompleteUpload;
use App\Models\Exam;
use App\Models\Subject;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\WithPagination;

class SubjectExamScores extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public Exam $exam;
    public Subject $subject;
    public $level;
    public $levelUnit;
    public $segments;

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

        $levelId = optional($level)->id ?? optional(optional($levelUnit)->level)->id;

        if(!empty($subject->segments)){
            if(array_key_exists($levelId, $subject->segments)){
                $this->segments = $subject->segments[$levelId];
            }
        }
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
            ->leftJoin("{$tblName}", "students.id", "=", "{$tblName}.student_id");

        $dbDriver = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME);

        if($dbDriver == 'mysql') $query->selectRaw("students.id, students.adm_no, students.name, `{$tblName}`.{$col}, CAST(JSON_UNQUOTE(JSON_EXTRACT(`{$tblName}`.$col,\"$.score\")) AS UNSIGNED) AS score");

        else $query->select("students.id", "students.adm_no", "students.name", "{$tblName}.{$col}");

        if ($this->level) $query->where('students.level_id', $this->level->id);

        if (!is_null($this->levelUnit)) $query->where('students.level_unit_id', $this->levelUnit->id);

        if($dbDriver == 'mysql') $query->orderBy('score', "DESC");
        
        else $query->orderBy('name');
            
        return $query->paginate(24)->withQueryString();
    }

    /** 
     * Ranks the students that did the subject in question
     */
    public function rankSubjectResults()
    {
        try {
    
            CompleteUpload::rank($this->exam, $this->subject, $this->level, $this->levelUnit);

            session()->flash("status", "Students {$this->subject->name} rank successfully generated and stored");

            $this->emit('hide-generate-rank');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-generate-rank');
        }
    }

    /**
     * Calculate and update total score for each subject
     */
    public function calculateTotalScore()
    {
        try {
    
            CompleteUpload::calculateTotals($this->exam, $this->subject, $this->level, $this->levelUnit);
            
            session()->flash("status", "Students {$this->subject->name} totals successfully generated and stored");

            $this->exam = $this->exam->fresh();

            $this->emit('hide-generate-totals');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-generate-totals');
        }
        
    }

    /** 
     * Calculate student exam deviations and saving the deviations
     */
    public function calculateAndUpdateDeviations()
    {
        try {

            $result = CompleteUpload::calculateDeviations($this->exam, $this->subject, $this->level, $this->levelUnit);

            if ($result) {                
                session()->flash("status", "Deviations successfully calculated, saved and deviation ranks generated");
    
                $this->exam = $this->exam->fresh();
            }

            $this->emit('hide-generate-totals');
            
        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);

            session()->flash('error', $exception->getMessage());

            $this->emit('hide-generate-totals');
            
        }
        
    }
}
