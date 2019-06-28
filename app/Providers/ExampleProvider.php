<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Illuminate\Support\ServiceProvider;

class ExampleProvider extends EventServiceProvider
{
    public function shouldDiscoverEvents()
    {
        return true;
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // This service provider needs to subscribe any discovered listeners...
        // The discovered listeners might be in `artisan event:list` but not actually have any event subscriptions.
    }
}
