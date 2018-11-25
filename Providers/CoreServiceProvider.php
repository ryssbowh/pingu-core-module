<?php

namespace Modules\Core\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Modules\Core\Facades\Settings;

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
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->registerWebMiddlewares($router);
        $this->registerGlobalMiddlewares($kernel);
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->registerSettings();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }

    public function registerSettings(){
        $this->app->singleton('settings','Modules\Core\Settings');
        Settings::init();
        $settings = [
            'mail.driver' => [
                'title' => 'Email driver',
                'section' => 'Emails'
            ],
            'mail.username' => [
                'title' => 'Email username',
                'section' => 'Emails'
            ],
            'mail.port' => [
                'title' => 'Email port',
                'section' => 'Emails'
            ],
            'mail.password' => [
                'title' => 'Email password',
                'section' => 'Emails'
            ],
            'app.name' => [
                'title' => 'Site name',
                'section' => 'general'
            ],
            'session.lifetime' => [
                'title' => 'Session lifetime',
                'section' => 'Session'
            ]
        ];
        $encrypt = [
            'mail.username',
            'mail.password'
        ];
        Settings::registerMany( $settings, $encrypt );
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
        $viewPath = resource_path('views/modules/core');

        $sourcePath = __DIR__.'/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/core';
        }, \Config::get('view.paths')), [$sourcePath]), 'core');

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
        return [
        ];
    }
}
