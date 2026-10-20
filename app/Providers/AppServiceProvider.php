<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
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
        // Force HTTPS for every generated URL in production (behind Render/proxies).
        if ($this->app->isProduction() && !$this->app->runningInConsole()) {
            URL::forceScheme('https');
        }

        // Section permission gate — used by the `section` route middleware and
        // by @can('section', 'x') checks in the admin sidebar/views.
        Gate::define('section', function (User $user, string $section) {
            return $user->canAccess($section);
        });

        $this->registerRateLimiters();
    }

    /**
     * Throttle public, abuse-prone endpoints.
     */
    protected function registerRateLimiters(): void
    {
        // Contact / mail forms: 5 submissions per minute per IP.
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Other public forms (join requests): 10 per minute per IP.
        RateLimiter::for('forms', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        // Authenticated rich-text editor image uploads: generous, keyed per user
        // so a public form can never exhaust an admin's upload budget.
        RateLimiter::for('uploads', function (Request $request) {
            return Limit::perMinute(30)->by((string) ($request->user()?->id ?? $request->ip()));
        });

        // Admin login: 6 attempts per minute per email + IP (brute-force guard).
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(6)->by((string) $request->input('email') . '|' . $request->ip());
        });
    }
}
