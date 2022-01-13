<?php

namespace App\View\Components\Exams\Scores;

use App\Models\Exam;
use Illuminate\View\Component;

class LevelScores extends Component
{
    public Exam $exam;

    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     *
     * @return void
     */
    public function __construct(Exam $exam)
    {
        $this->exam = $exam;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.scores.level-scores');
    }

    /**
     * Retrieves the current exam level with published scores
     * 
     * @return Collection
     */
    public function levelsWithScores()
    {
        return $this->exam->levels;
    }
}
