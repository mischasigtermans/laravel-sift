<?php

namespace MischaSigtermans\Sift;

use Illuminate\Support\ServiceProvider;
use MischaSigtermans\Sift\Facades\Sift as SiftFacade;

class SiftServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/sift.php', 'sift');

        $this->app->singleton(Sift::class, function ($app) {
            return new Sift;
        });

        $this->app->alias(Sift::class, SiftFacade::class);
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/sift.php' => config_path('sift.php'),
        ], 'sift-config');
    }
}
