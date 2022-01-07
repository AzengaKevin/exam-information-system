<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use App\Settings\SystemSettings;
use Illuminate\View\Component;
use Illuminate\Database\Eloquent\Collection;

class LevelSubjectPerformance extends Component
{
    public Level $level;
    public Exam $exam;

    /**
     * Create a new component instance.
     * 
     * @param Exam $exam
     * @param Level $level
     *
     * @return void
     */
    public function __construct(Exam $exam, Level $level)
    {
        $this->exam = $exam;
        $this->level = $level;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-subject-performance');
    }

    /**
     * Get level subjects with performamnce
     * 
     * @return Collection
     */
    public function subjects()
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $orderByCol = ($systemSettings->school_level === 'secondary')
            ? 'points'
            : 'average';

        return $this->exam->levelSubjectPerformance()
            ->wherePivot('level_id', $this->level->id)
            ->orderByPivot($orderByCol, 'desc')
            ->get();
    }
}
