<?php

namespace App\Providers;

use App\Channels\AdvantaChannel;
use App\Models\Guardian;
use App\Models\Message;
use App\Models\Teacher;
use App\Models\User;
use App\Observers\MessageObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Notification;
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
            'guardian' => Guardian::class,
            'user' => User::class,
        ]);

        // Register new notification channel
        Notification::extend('advanta', fn($app) => new AdvantaChannel());

        // Register Observers
        Message::observe(MessageObserver::class);
    }
    
}
