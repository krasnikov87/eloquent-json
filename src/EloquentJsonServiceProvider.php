<?php

namespace Krasnikov\EloquentJSON;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use Krasnikov\EloquentJSON\Exceptions\Handler;

class EloquentJsonServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'EloquentJson');
        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/EloquentJson')
        ], 'translations');
        $this->publishes([
            __DIR__ . '/../config/jsonSpec.php' => config_path('jsonSpec.php')
        ], 'config');
        $this->mergeConfigFrom(__DIR__ . '/../config/jsonSpec.php', 'jsonSpec');
    }
    public function register()
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }
}