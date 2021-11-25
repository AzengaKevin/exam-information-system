<?php

namespace App\Providers;

use App\Models\Guardian;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Change paginator to bootstrap
        Paginator::useBootstrap();

        Relation::enforceMorphMap([
            'teacher' => Teacher::class,
            'guardian' => Guardian::class
        ]);
    }
}
