<?php

namespace Pingu\Core\Providers;

use Asset, View, Theme, Blade, Settings;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Pingu\Forms\Fields\Number;
use Pingu\Forms\Fields\Text;
use Spatie\TranslationLoader\LanguageLine;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    protected $webMiddlewares = [
        'homepage' => \Pingu\Core\Http\Middleware\HomepageMiddleware::class
        
    ];

    protected $globalMiddlewares = [
        \Pingu\Core\Http\Middleware\SetThemeMiddleware::class
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
        $this->registerFactories();
        $this->registerAssets();
        $this->registerCommands();
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'core');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        /**
         * Add dump function to blade
         */
        Blade::directive('d', function ($data) {
            return sprintf("<?php dump(%s); ?>",
                'all' !== $data ? "get_defined_vars()['__data']" : $data
            );
        });

        /**
         * Register settings
         */
        Settings::registerMany([
            'app.name' => [
                'Title' => 'Site name',
                'Section' => 'Core',
                'type' => Text::class,
                'validation' => 'required'
            ],
            'session.lifetime' => [
                'Title' => 'Session Lifetime',
                'Section' => 'Core',
                'type' => Number::class,
                'validation' => 'required|integer'
            ]
        ]);
    }

    /**
     * Registers commands for this module
     * @return void
     */
    public function registerCommands(){
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Pingu\Core\Console\MergePackages::class
            ]);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('core.textSnippet', \Pingu\Core\Components\TextSnippet::class);
        $this->app->bind('core.contextualLinks', \Pingu\Core\Components\ContextualLinks::class);
        $this->app->bind('core.notify', \Pingu\Core\Components\Notify::class);
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
        Asset::container('vendor')->add('js-manifest', 'manifest.js');
        Asset::container('vendor')->add('js-vendor', 'vendor.js');
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
