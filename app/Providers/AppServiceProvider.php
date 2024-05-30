<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Logger;
use App\Services\ResponseService;
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
        $this->app->bind(ResponseService::class, fn () => new ResponseService);
        $this->app->bind(Logger::class, fn () => new Logger);
    }
}
