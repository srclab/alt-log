<?php

namespace SrcLab\AltLog;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class AltLogServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (! defined('ALT_LOG_PACKAGE_PATH')) {
            define('ALT_LOG_PACKAGE_PATH', realpath(__DIR__.'/../'));
        }

        $this->registerConfigs();
        $this->registerCommands();
        $this->registerPublishes();

        $this->app->singleton( \SrcLab\AltLog\Contracts\AltLog::class,AltLog::class);
        $this->app->alias(\SrcLab\AltLog\Contracts\AltLog::class,'srclab.alt_log');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerResources();
        $this->registerTranslations();
        $this->registerRoutes();
    }

    /**
     * Register the routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        $domain = config('alt-log.route.domain');
        $path = config('alt-log.route.path');
        $middleware = config('alt-log.route.middleware');

        if (empty($path) || empty($middleware)) {
            return;
        }

        Route::group([
            'domain' => $domain,
            'prefix' => $path,
            'middleware' => $middleware,
            'as' => 'alt-log::',
            'namespace' => 'SrcLab\AltLog\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(ALT_LOG_PACKAGE_PATH.'/routes/web.php');
        });
    }

    /**
     * Register the resources.
     *
     * @return void
     */
    protected function registerResources()
    {
        $this->loadViewsFrom(ALT_LOG_PACKAGE_PATH.'/resources/views', 'alt-log');
    }

    /**
     * Register the rranslations.
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(ALT_LOG_PACKAGE_PATH.'/resources/lang', 'alt-log');
    }

    /**
     * Register configs.
     *
     * @return void
     */
    protected function registerConfigs()
    {
        $this->mergeConfigFrom(ALT_LOG_PACKAGE_PATH.'/config/config.php', 'alt-log');
    }

    /**
     * Register publishes.
     *
     * @return void
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {

            $this->publishes([
                ALT_LOG_PACKAGE_PATH.'/config/config.php' => config_path('alt-log.php'),
            ], 'alt-log-config');

            $this->publishes([
                ALT_LOG_PACKAGE_PATH.'/public' => public_path('vendor/alt-log'),
            ], 'alt-log-assets');
        }
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\AssetsCommand::class,
                Console\InstallCommand::class,
            ]);
        }
    }
}
