<?php

namespace App\Providers;

use App\Services\ParserService;
use App\Services\ReplayService;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use Schema;

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
        Schema::defaultStringLength(191);

        Resource::withoutWrapping();
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
