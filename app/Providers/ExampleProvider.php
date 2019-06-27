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
        // If this is called duplicate listeners are registered per provider that are doing discovery...
        parent::boot();
    }
}
