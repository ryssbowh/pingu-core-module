<?php

namespace Pingu\Core\Providers;

use Asset, View, Theme, Blade, Settings;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Pingu\Core\Console\GenerateDoc;
use Pingu\Core\Console\MakeComposer;
use Pingu\Core\Console\MakeException;
use Pingu\Core\Console\MergePackages;
use Pingu\Core\Http\Middleware\ActivateDebugBar;
use Pingu\Core\Http\Middleware\CheckForMaintenanceMode;
use Pingu\Core\Http\Middleware\HomepageMiddleware;
use Pingu\Core\Http\Middleware\RedirectIfAuthenticated;
use Pingu\Core\Http\Middleware\SetThemeMiddleware;
use Pingu\Core\ModelRoutes;
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

    protected $modelFolder = 'Entities';

    protected $routeMiddlewares = [
        'home' => HomepageMiddleware::class,
        'guest' => RedirectIfAuthenticated::class,
    ];

    protected $groupMiddlewares = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            CheckForMaintenanceMode::class,
            ActivateDebugBar::class,
            SetThemeMiddleware::class
        ],
        'ajax' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            SetThemeMiddleware::class
        ],
    ];

    protected $globalMiddlewares = [];

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        $this->registerModelSlugs();
        $this->registerGroupMiddlewares($router);
        $this->registerRouteMiddlewares($router);
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
    }

    /**
     * Registers commands for this module
     * @return void
     */
    public function registerCommands(){
        if ($this->app->runningInConsole()) {
            $this->commands([
                MergePackages::class,
                MakeComposer::class,
                MakeException::class,
                GenerateDoc::class
            ]);
        }
    }

    /**
     * Registers all the slugs for this module's models
     */
    public function registerModelSlugs()
    {
        \ModelRoutes::registerSlugsFromPath(realpath(__DIR__.'/../'.$this->modelFolder));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('core.contextualLinks', \Pingu\Core\Components\ContextualLinks::class);
        $this->app->singleton('core.notify', \Pingu\Core\Components\Notify::class);
        $this->app->singleton('core.themeConfig', \Pingu\Core\Components\ThemeConfig::class);
        $this->app->singleton('core.modelRoutes', ModelRoutes::class);
        $this->app->register(RouteServiceProvider::class);
    }

    public function registerRouteMiddlewares(Router $router)
    {
        foreach($this->routeMiddlewares as $name => $middleware){
            $router->aliasMiddleware($name, $middleware);
        }
    }

    public function registerGlobalMiddlewares(Kernel $kernel)
    {
        foreach($this->globalMiddlewares as $middleware){
            $kernel->pushMiddleware($middleware);
        }
    }

    public function registerGroupMiddlewares(Router $router)
    {
        foreach($this->groupMiddlewares as $group => $middlewares){
            foreach($middlewares as $middleware){
                $router->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }

    public function registerAssets()
    {
        Asset::addVersioning();
        Asset::container('vendor')->add('js-manifest', 'manifest.js');
        Asset::container('vendor')->add('js-vendor', 'vendor.js');
        Asset::container('modules')->add('core-js', 'module-assets/Core/js/Core.js');
        Asset::container('modules')->add('core-css', 'module-assets/Core/css/Core.css');
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
        $this->mergeConfigFrom(
            __DIR__.'/../Config/modules.php', 'modules'
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
