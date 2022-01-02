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
            'livewire.exam-levels',
            'livewire.level-exam-results',
            'students.index',
            'teachers.responsibilities.index',
            'teachers.show',
            'exams.scores.index',
            'exams.results.index',
            'exams.transcripts.index',
        ], SettingsComposer::class);
    }
}
