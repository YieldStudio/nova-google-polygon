<?php

namespace YieldStudio\NovaGooglePolygon;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config' => config_path(),
            ], 'nova-google-polygon-config');
        }

        Nova::serving(function (ServingNova $event) {
            Nova::script('google-polygon', __DIR__ . '/../dist/js/field.js');

            Nova::provideToScript([
                'googlePolygon' => [
                    'key' => config('nova-google-polygon.api_key'),
                ],
            ]);
        });
    }
}
