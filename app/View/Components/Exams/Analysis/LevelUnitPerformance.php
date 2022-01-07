<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use App\Settings\SystemSettings;
use Illuminate\View\Component;

class LevelUnitPerformance extends Component
{
    public Level $level;
    public Exam $exam;
    /**
     * Create a new component instance.
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
        return view('components.exams.analysis.level-unit-performance');
    }

    /**
     * Get exam level units with scores
     * 
     * @return Collection
     */
    public function levelUnits()
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        $orderByCol = ($systemSettings->school_level === 'secondary')
            ? 'points'
            : 'average';

        return $this->exam->levelUnits()
            ->orderByPivot($orderByCol, 'desc')
            ->where('level_units.level_id', $this->level->id)
            ->get();
    }

    /**
     * Get the appropriate column count
     */
    public function colsCount() : int
    {
        /** @var SystemSettings */
        $systemSettings = app(SystemSettings::class);

        return ($systemSettings->school_level === 'secondary')
            ? 5
            : 4;
        
    }
}
