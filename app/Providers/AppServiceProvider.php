<?php

namespace App\Providers;

use App\Services\ParserService;
use App\Services\ReplayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(ParserService::class, function () {
            return new ParserService();
        });

        $this->app->singleton(ReplayService::class, function () {
            return new ReplayService(new ParserService());
        });

        # migration fix for mysql < 5.7.7, needed for travis
        \Schema::defaultStringLength(191);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
