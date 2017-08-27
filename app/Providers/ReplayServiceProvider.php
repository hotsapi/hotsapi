<?php

namespace App\Providers;

use App\Services\ReplayService;
use Illuminate\Support\ServiceProvider;

class ReplayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ReplayService::class, function () {
            return new ReplayService();
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
