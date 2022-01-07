<?php

namespace App\Http\Livewire;

use App\Models\Exam;
use App\Settings\SystemSettings;
use Livewire\Component;

class ExamLevels extends Component
{
    /** @var Exam */
    public $exam;

    /**
     * Called when components mount on a view
     * 
     * @param Exam $exam
     */
    public function mount(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get all exam levels in the order of performance if published and specifically the class average
     */
    public function getLevels()
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $orderByColumn = ($systemSettings->school_level === 'primary')
            ? 'average'
            : 'points';

        return $this->exam->levels()->orderByPivot($orderByColumn, 'desc')->get();
    }

    public function render()
    {
        return view('livewire.exam-levels', [
            'levels' => $this->getLevels()
        ]);
    }
}
