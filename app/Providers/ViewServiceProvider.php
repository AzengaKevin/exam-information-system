<?php

namespace App\Providers;

use App\View\Composers\SettingsComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer([
            'components.navbar',
            'layouts.dashboard',
            'dashboard',
            'levels.index',
            'livewire.students',
            'livewire.teacher-responsibilities',
            'livewire.subject-exam-scores',
            'livewire.exam-levels',
            'livewire.level-exam-results',
            'livewire.level-exam-scores',
            'livewire.level-unit-exam-scores',
            'students.index',
            'teachers.responsibilities.index',
            'teachers.show',
            'exams.scores.index',
            'exams.results.index',
            'exams.transcripts.index',
            'components.exams.analysis.level-line-graph',
            'exams.analysis.index',
            'exams.scores.upload',
            'exams.scores.manage',
            'components.modals.students.add',
            'components.exams.analysis.level-subject-performance',
            'components.exams.analysis.level-unit-subject-performance',
            'components.exams.analysis.level-student-performance',
            'components.exams.analysis.level-most-improved-students',
            'components.exams.analysis.level-unit-most-improved-students',
            'components.exams.analysis.level-most-dropped-students',
            'components.exams.analysis.level-unit-most-dropped-students',
            'components.exams.analysis.level-unit-student-performance',
            'components.exams.analysis.level-unit-performance',
            'components.exams.analysis.level-streams-subject-rank'
        ], SettingsComposer::class);
    }
}
