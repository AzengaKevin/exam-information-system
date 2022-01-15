<?php

namespace App\Http\Livewire;

use App\Actions\Exam\CreateScoresTable;
use App\Models\Exam;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Livewire\Component;

class ExamQuickActions extends Component
{
    /** @var Exam */
    public $exam;

    public $status;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;

        $this->status = $exam->status;
    }

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

            CreateScoresTable::updateScoresTable($this->exam);
            
            session()->flash('status', "{$this->exam->name} scores table has been updated");

            $this->emit('hide-update-scores-table-modal');

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            session()->flash('error', "Failed creating {$this->exam->name} scores table");

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

            if($this->exam->update($data)){

                if ($this->exam->fresh()->isInMarking()) {

                    CreateScoresTable::invoke($this->exam);

                }

                session()->flash('status', 'Exam status successfully changed');

                $this->emit('hide-change-exam-status-modal');
            }


        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            session()->flash('error', "Failed changing {$this->exam->name} status");

            $this->emit('hide-change-exam-status-modal');
            
        }
        
    }
}
