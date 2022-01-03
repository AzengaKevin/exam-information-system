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
            'components.modals.students.add'
        ], SettingsComposer::class);
    }
}
