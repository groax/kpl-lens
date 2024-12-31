<?php

namespace App\Providers;

use App\Events\AgendaDeleted;
use App\Events\AgendaSaved;
use App\Events\AgendaUpdated;
use App\Listeners\HandleAgendaDeleted;
use App\Listeners\HandleAgendaSaved;
use App\Listeners\HandleAgendaUpdated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Event::listen(AgendaSaved::class, [HandleAgendaSaved::class, 'handle']);
         Event::listen(AgendaUpdated::class, [HandleAgendaUpdated::class, 'handle']);
         Event::listen(AgendaDeleted::class, [HandleAgendaDeleted::class, 'handle']);
    }
}
