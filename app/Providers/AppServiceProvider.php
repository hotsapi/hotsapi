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
