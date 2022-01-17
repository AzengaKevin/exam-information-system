<?php

namespace App\View\Components\Exams\Analysis;

use App\Models\Exam;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\View\Component;
use App\Settings\SystemSettings;

class LevelStreamsSubjectRank extends Component
{
    public Exam $exam;
    public Level $level;
    public Subject $subject;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Exam $exam,Level $level, Subject $subject)
    {
        $this->exam = $exam;
        $this->level = $level;
        $this->subject = $subject;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.exams.analysis.level-streams-subject-rank');
    }

    /**
     * Get same subjects for level units but different streams ande average ofcourse
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

        return $this->exam->levelUnitSubjectPerformance()
            ->wherePivotIn('level_unit_id', $this->level->levelUnits->pluck('id')->all())
            ->orderByPivot($orderByCol, 'desc')
            ->where('subject_id', $this->subject->id)
            ->get();
    }
}
