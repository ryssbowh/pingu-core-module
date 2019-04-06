<?php

namespace Modules\Core\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Spatie\TranslationLoader\LanguageLine;
use Asset, View, Theme;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $webMiddlewares = [
        'homepage' => \Modules\Core\Http\Middleware\HomepageMiddleware::class
        
    ];

    protected $globalMiddlewares = [
        \Modules\Core\Http\Middleware\SetThemeMiddleware::class
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        $this->registerWebMiddlewares($router);
        $this->registerGlobalMiddlewares($kernel);
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->registerAssets();
        $this->registerCommands();
        
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
    }

    /**
     * Registers commands for this module
     * @return void
     */
    public function registerCommands(){
        $this->commands([
            \Modules\Core\Console\MergePackages::class
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('core.textSnippet', \Modules\Core\Components\TextSnippet::class);
        $this->app->bind('core.contextualLinks', \Modules\Core\Components\ContextualLinks::class);
        $this->app->bind('core.notify', \Modules\Core\Components\Notify::class);

        $this->app->singleton('view.finder', function ($app) {
            return new \Modules\Core\Components\themeViewFinder(
                $app['files'],
                $app['config']['view.paths'],
                null
            );
        });

        // $providers = config('app.providers');
        // $index = array_search("Illuminate\Translation\TranslationServiceProvider", $providers);
        // unset($providers[$index]);
        // $providers[] = "Spatie\TranslationLoader\TranslationServiceProvider";
        // config(['app.providers' => $providers]);
    }

    public function registerWebMiddlewares(Router $router)
    {
        foreach( $this->webMiddlewares as $name => $middleware){
            $router->pushMiddlewareToGroup('web',$middleware);
        }
    }

    public function registerGlobalMiddlewares(Kernel $kernel)
    {
        foreach( $this->globalMiddlewares as $middleware){
            $kernel->pushMiddleware($middleware);
        }
    }

    public function registerAssets()
    {
        Asset::addVersioning();
        Asset::container('vendor')->add('js-manifest', 'themes/Default/js/manifest.js');
        Asset::container('vendor')->add('js-vendor', 'themes/Default/js/vendor.js');
        Asset::container('modules')->add('core-js', 'modules/Core/js/Core.js');
        Asset::container('modules')->add('core-css', 'modules/Core/css/Core.css');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__.'/../Config/config.php' => config_path('core.php'),
        ], 'config');
        $this->mergeConfigFrom(
            __DIR__.'/../Config/config.php', 'core'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $themePaths = $this->app->make('view.finder')->getThemesPublishPaths('core');

        $sourcePath = __DIR__.'/../Resources/views';

        foreach($themePaths as $path => $namespace){
            $this->publishes([
                $sourcePath => $path
            ],$namespace);
        }
        
        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/core';
        }, \Config::get('view.paths')), [$sourcePath]), 'core');

        View::share( 'contextualLinks', $this->app->make('core.contextualLinks') );
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/core');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'core');
        } else {
            $this->loadTranslationsFrom(__DIR__ .'/../Resources/lang', 'core');
        }
    }

    /**
     * Register an additional directory of factories.
     * 
     * @return void
     */
    public function registerFactories()
    {
        if (! app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/../Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
