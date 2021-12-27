<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Models\Subject;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubjectExamScores extends Component
{
    public Exam $exam;
    public Subject $subject;
    public $level;
    public $levelUnit;

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

    public function getRelevantExamData()
    {
        $tblName = Str::slug($this->exam->shortname);

        $col = $this->subject->shortname;

        $query = DB::table('students')
            ->leftJoin("{$tblName}", "students.adm_no", "=", "{$tblName}.admno")
            ->select("students.adm_no", "students.name", "{$tblName}.{$col}");

        if ($this->level) $query->where('students.level_id', $this->level->id);

        if (!is_null($this->levelUnit)) $query->where('students.level_unit_id', $this->levelUnit->id);
            
        return $query->get();
        
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
            $query = DB::table($tblName)->selectRaw("admno, JSON_UNQUOTE(JSON_EXTRACT($col,\"$.score\")) AS score");
    
            if(!is_null($this->level)) $query->where("level_id", $this->level->id);
    
            if(!is_null($this->levelUnit)) $query->where('level_unit_id', $this->levelUnit->id);
    
            /** @var Collection */
            $data = $query->orderBy("score", 'desc')->get();
    
            // Get the total records count
            $total = $data->count();
    
            $data->each(function($item, $key) use ($tblName, $col, $total){

                $rank = $key + 1;
    
                $result = DB::update("UPDATE `$tblName` SET `$col` = JSON_SET(`$col`, \"$.rank\", $rank, \"$.total\", $total) WHERE admno = {$item->admno}");

                Log::debug($result);

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
}
