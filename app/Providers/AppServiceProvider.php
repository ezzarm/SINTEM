<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Force HTTPS in production ──────────────────────────────────
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on'); // ← tambahan ini
        }

        // ── Allow APP_DEBUG=true in production for Railway debugging ──
        // Uncomment the block below AFTER debugging is complete:
        // if ($this->app->environment('production') && config('app.debug')) {
        //     Log::critical('APP_DEBUG is true in production! Forcing to false.');
        //     config(['app.debug' => false]);
        // }

        // ── Log slow DB queries (> 2 seconds) in production ───────────
        if ($this->app->environment('production')) {
            DB::whenQueryingForLongerThan(2000, function () {
                Log::warning('Slow database query detected (> 2s).');
            });
        }
    }
}