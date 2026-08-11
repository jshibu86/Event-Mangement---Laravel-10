<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\{Game, Program};
use App\Models\AppSetting;
use Illuminate\Support\Facades\Schema;

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
    // public function boot(): void
    // {
    //     Relation::morphMap(['game' => Game::class, 'program' => Program::class]);
    //     if (Schema::hasTable('app_settings')) {
    //         view()->share('brandName', AppSetting::value('site_name', 'Festiva'));
    //         view()->share('brandLogo', AppSetting::value('logo'));
    //     }
    // }
    public function boot(): void
    {
        Relation::morphMap([
            'game' => Game::class,
            'program' => Program::class,
        ]);

        if (!app()->runningInConsole() && Schema::hasTable('app_settings')) {
            view()->share('brandName', AppSetting::value('site_name', 'Festiva'));
            view()->share('brandLogo', AppSetting::value('logo'));
        }
    }
}
