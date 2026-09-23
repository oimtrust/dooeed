<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        RateLimiter::for('email-otp', function (Request $request): Limit {
            return Limit::perMinutes(10, 3)
                ->by(Str::lower((string) $request->input('email')).'|'.$request->ip())
                ->response(fn () => response()->json(['message' => 'Too many requests, try again later'], 429));
        });
    }
}
