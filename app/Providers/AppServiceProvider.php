<?php

namespace App\Providers;

use App\Services\SupabaseStorageService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SupabaseStorageService::class, function ($app) {
            return new SupabaseStorageService();
        });
    }

    public function boot(): void
    {
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = auth()->user();
            if (!$user) return route('login');

            return match((int) $user->role_id) {
                1       => route('superadmin.accounts.index'),
                2       => route('pengumuman.index'),
                default => route('admin.pengumuman.index'),
            };
        });
    }
}
