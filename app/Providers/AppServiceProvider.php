<?php

namespace App\Providers;

use App\Http\Controllers\IOT\IotDataController;
use App\Listeners\LandownerCreatedListener;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(LandownerCreatedListener::class);

        $this->app->when(IotDataController::class)
            ->needs('$landId')
            ->give(function ($app) {
                // Fetch the land ID from LandownerCreatedListener
                $listener = $app->make(LandownerCreatedListener::class);
                return $listener->getUniqueLandId();
            });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

    }
}
