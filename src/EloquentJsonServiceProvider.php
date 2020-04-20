<?php

namespace Krasnikov\EloquentJSON;

use Carbon\Carbon;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Krasnikov\EloquentJSON\Exceptions\Handler;

/**
 * Class EloquentJsonServiceProvider
 * @package Krasnikov\EloquentJSON
 */
class EloquentJsonServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'EloquentJson');
        $this->publishes(
            [
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/EloquentJson')
            ],
            'translations'
        );
        $this->publishes(
            [
                __DIR__ . '/../config/jsonSpec.php' => config_path('jsonSpec.php')
            ],
            'config'
        );
        $this->mergeConfigFrom(__DIR__ . '/../config/jsonSpec.php', 'jsonSpec');

        Carbon::serializeUsing(
            function ($carbon) {
                return $carbon->format(Config::get('jsonSpec.date_format'));
            }
        );
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ExceptionHandler::class, Handler::class);
    }
}
