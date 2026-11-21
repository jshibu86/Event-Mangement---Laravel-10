<?php

namespace App\Providers;

use App\Models\{Game, Program, AppSetting};
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Relation::morphMap([
            'game'    => Game::class,
            'program' => Program::class,
        ]);

        // Force https only when APP_URL itself is https
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        if (!app()->runningInConsole() && Schema::hasTable('app_settings')) {
            $logo = AppSetting::value('logo');

            view()->share('brandName', AppSetting::value('site_name', 'Festiva'));
            view()->share('brandLogo', $logo);                          // raw path
            view()->share('brandLogoUrl', AppSetting::fileUrl($logo));  // ready-to-use URL (null if no logo)
        }
    }
}