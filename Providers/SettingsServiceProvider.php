<?php

namespace Pingu\Core\Providers;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;
use Pingu\Core\Http\Middleware\EditSettings;
use Pingu\Core\Http\Middleware\IndexSettings;
use Pingu\Core\Settings\ConfigRepository;
use Pingu\Core\Settings\Settings;
use Pingu\Core\Support\ModuleServiceProvider;

class SettingsServiceProvider extends ModuleServiceProvider
{
    protected $routeMiddlewares = [
        'indexSettings' => IndexSettings::class,
        'editSettings' => EditSettings::class
    ];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->registerRouteMiddlewares($router);
        \Settings::bootRepositories();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $settings = new Settings;
        $config = $this->app['config']->all();
        $this->app->singleton('settings', function () use ($settings) {
            return $settings;
        });
        //Replace Laravel Config repository
        $this->app->singleton('config', function ($app) use ($config, $settings) {
            return new ConfigRepository($config, $settings);
        });
        //Binds settings section slug in Route system
        $app = $this->app;
        \Route::bind('setting_section', function ($value, $route) use ($app) {
            return $app->make('settings.'.$value);
        });
    }

    public function registerRouteMiddlewares(Router $router)
    {
        foreach ($this->routeMiddlewares as $name => $middleware) {
            $router->aliasMiddleware($name, $middleware);
        }
    }
}
