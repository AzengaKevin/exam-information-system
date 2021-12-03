<?php

namespace App\Http\Livewire;

use App\Actions\Exam\CreateScoresTable;
use App\Models\Exam;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ExamQuickActions extends Component
{
    public $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    public function render()
    {
        return view('livewire.exam-quick-actions');
    }

    public function createScoresTable()
    {
        try {

            CreateScoresTable::invoke($this->exam);
            
            session()->flash('status', "{$this->exam->name} scores table has been created");

        } catch (\Exception $exception) {

            Log::error($exception->getMessage(), [
                'action' => __METHOD__
            ]);
            
            session()->flash('error', "Failed creating {$this->exam->name} scores table");
        }
        
    }
}
