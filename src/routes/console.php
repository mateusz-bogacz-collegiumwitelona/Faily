<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\ClearViewCache;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('events:send-reminders')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/event-reminders.log'));

Artisan::command('all-clear', function () {
    Cache::flush();
    $this->info('App cache has been cleared');

    Artisan::call('view:clear');
    $this->info('View cache has been cleared');

    Artisan::call('view:clear');
    $this->info('View cache has been cleared');

    Artisan::call('route:clear');
    $this->info('Route cache has been cleared');

    Artisan::call('config:clear');
    $this->info('Config cache has been cleared');

    Artisan::call('optimize:clear');
    $this->info('Optimized cache has been cleared');

    $availableLocales = ['pl', 'en', 'jpn', 'es', 'ua'];

    foreach ($availableLocales as $locale) {
        Cache::forget('translations_{$locale}');
        $this->info("Cache cleared for locale {$locale}");
    }

    foreach ($availableLocales as $locale) {
        $keys = Cache::get('navbar_keys', []);
        foreach ($keys as $key) {
            if (strpos($key, "navbar_{$locale}_") === 0) {
                Cache::forget($key);
                $this->info("Cleared cache for {$key}");
            }
        }

        Cache::forget("footer_{$locale}");
        $this->info("Cleared cache for footer_{$locale}");
    }

    $this->info("Cache cleared");
})->purpose('Clear all application caches including Redis, views, routes, config, etc.');
