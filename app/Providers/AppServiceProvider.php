<?php

namespace App\Providers;

use App\Services\BigQuery;
use App\Services\ParserService;
use App\Services\ReplayService;
use DB;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Support\ServiceProvider;
use Log;
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

        $this->app->singleton(BigQuery::class, function () {
            return new BigQuery();
        });

        # migration fix for mysql < 5.7.7, needed for travis
        Schema::defaultStringLength(255);

        Resource::withoutWrapping();

        if (env('DB_LOG_QUERIES', false)) {
            DB::listen(function ($query) {
                Log::info('Query', [$query->sql, $query->time, $query->connectionName]);
            });
        }
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
