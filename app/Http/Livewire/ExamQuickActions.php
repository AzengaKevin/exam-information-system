<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Actions\Exam\CreateScoresTable;

class ExamQuickActions extends Component
{
    public Exam $exam;

    public $status;

    /**
     * Lifecycle method that executes only once when the component is launching
     * 
     * @param Exam $exam
     */
    public function mount(Exam $exam)
    {
        $this->exam = $exam;

        $this->status = $exam->status;
    }

    /**
     * Lifecycle method that renders he component everytime the state of the component changes
     * 
     * @return View
     */
    public function render()
    {
        return view('livewire.exam-quick-actions', [
            'statuses' => Exam::examStatusOptions()
        ]);
    }

    /**
     * Add the columns that does not exist in the current exam scores table
     */
    public function updateScoresTable()
    {
        try {

            DB::transaction(function(){

                CreateScoresTable::updateScoresTable($this->exam);

                $this->exam->userActivities()->attach(Auth::id(), ['action' => 'Updated The Exam Scores Table']);

            });

            
            session()->flash('status', "{$this->exam->name} scores table has been updated");

            $this->emit('hide-update-scores-table-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            session()->flash('error', "Failed updating {$this->exam->name} scores table");

            $this->emit('hide-update-scores-table-modal');
        }
        
    }

    /**
     * Change the status of the exam to any other available status
     */
    public function changeExamStatus()
    {
        $data = $this->validate(['status' => ['bail', 'required', Rule::in(Exam::examStatusOptions())]]);

        try {

            DB::transaction(function() use($data){

                if($this->exam->update($data)){
    
                    if ($this->exam->fresh()->isInMarking()) {
    
                        CreateScoresTable::invoke($this->exam);
    
                    }
    
                }

                $this->exam->userActivities()->attach(Auth::id(), ['action' => "Updated The Exam Status to {$this->exam->fresh()->status}"]);
            });

            
            session()->flash('status', 'Exam status successfully changed');

            $this->emit('hide-change-exam-status-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            session()->flash('error', "Failed changing {$this->exam->name} status");

            $this->emit('hide-change-exam-status-modal');
            
        }
        
    }
}
