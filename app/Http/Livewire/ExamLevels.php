<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use Livewire\Component;

class ExamLevels extends Component
{
    /** @var Exam */
    public $exam;

    public function mount(Exam $exam)
    {
        $this->exam = $exam;
        
    }

    public function getLevels()
    {
        return $this->exam->levels;
    }

    public function render()
    {
        return view('livewire.exam-levels', [
            'levels' => $this->getLevels()
        ]);
    }
}
