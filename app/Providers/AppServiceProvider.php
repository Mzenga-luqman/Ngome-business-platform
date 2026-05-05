<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('login', function (Request $request) {
            $identifier = strtolower(trim((string) $request->input('login', $request->input('email'))));

            return Limit::perMinute(5)->by($request->ip() . '|' . $identifier);
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        $maxImageMb = max(1, (int) config('uploads.product_image_max_mb', 10));

        // Keep Livewire's temporary upload validation in sync with product image limits.
        config([
            'livewire.csp_safe' => true,
            'livewire.temporary_file_upload.rules' => 'required|file|mimes:jpg,jpeg,png,webp|max:' . ($maxImageMb * 1024),
            'livewire.temporary_file_upload.max_upload_time' => max(5, (int) ceil($maxImageMb / 5)),
        ]);
    }
}
