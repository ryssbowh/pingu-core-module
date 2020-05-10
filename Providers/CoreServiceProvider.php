<?php

namespace Pingu\Core\Providers;

use Asset, View, Theme, Blade, Settings;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Routing\Router;
use Pingu\Core\Components\Accessors;
use Pingu\Core\Components\Actions;
use Pingu\Core\Components\ContextualLinks;
use Pingu\Core\Components\JsConfig;
use Pingu\Core\Components\Notify;
use Pingu\Core\Components\PinguCaches;
use Pingu\Core\Components\Policies;
use Pingu\Core\Components\RouteContext;
use Pingu\Core\Components\RouteSlugs;
use Pingu\Core\Components\Routes;
use Pingu\Core\Components\Uris;
use Pingu\Core\Config\CoreSettings;
use Pingu\Core\Config\MailingSettings;
use Pingu\Core\Http\Middleware\DeletableModel;
use Pingu\Core\Http\Middleware\EditSettings;
use Pingu\Core\Http\Middleware\EditableModel;
use Pingu\Core\Http\Middleware\HomepageMiddleware;
use Pingu\Core\Http\Middleware\IndexSettings;
use Pingu\Core\Http\Middleware\Published;
use Pingu\Core\Http\Middleware\RedirectIfAuthenticated;
use Pingu\Core\Http\Middleware\SetThemeMiddleware;
use Pingu\Core\Http\Middleware\StartSession;
use Pingu\Core\Settings\ConfigRepository;
use Pingu\Core\Settings\Settings as SettingsRepo;
use Pingu\Core\Support\ModuleServiceProvider;
use Pingu\Core\Test;
use Pingu\Core\Validation\CoreValidationRules;

class CoreServiceProvider extends ModuleServiceProvider
{
    protected $routeMiddlewares = [
        'home' => HomepageMiddleware::class,
        'guest' => RedirectIfAuthenticated::class,
        'deletableModel' => DeletableModel::class,
        'editableModel' => EditableModel::class,
        'indexSettings' => IndexSettings::class,
        'editSettings' => EditSettings::class,
        'published' => Published::class,
    ];

    protected $groupMiddlewares = [
        'web' => [
        ],
        'ajax' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]
    ];

    protected $globalMiddlewares = [
        StartSession::class,
        SetThemeMiddleware::class
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Test::register();

        $settings = new SettingsRepo;
        $config = $this->app['config']->all();
        $this->app->singleton(
            'settings', function () use ($settings) {
                return $settings;
            }
        );
        //Replace Laravel Config repository
        $this->app->singleton(
            'config', function ($app) use ($config, $settings) {
                return new ConfigRepository($config, $settings);
            }
        );
        $this->app->singleton('core.contextualLinks', ContextualLinks::class);
        $this->app->singleton('core.notify', Notify::class);
        $this->app->singleton('core.routeSlugs', RouteSlugs::class);
        $this->app->singleton('core.jsconfig', JsConfig::class);
        $this->app->singleton('core.uris', Uris::class);
        $this->app->singleton('core.routes', Routes::class);
        $this->app->singleton('core.actions', Actions::class);
        $this->app->singleton('core.policies', Policies::class);
        $this->app->singleton('core.caches', PinguCaches::class);
        $this->app->singleton('core.routeContext', RouteContext::class);

        \Settings::register(new CoreSettings, $this->app);
        \Settings::register(new MailingSettings, $this->app);
    }

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot(Router $router, Kernel $kernel)
    {
        $this->registerGroupMiddlewares($router);
        $this->registerRouteMiddlewares($router);
        $this->registerGlobalMiddlewares($kernel);
        \Config::loadSettings(\Settings::all());
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerFactories();
        $this->registerAssets();
        $this->registerJsConfig();
        $this->loadModuleViewsFrom(__DIR__ . '/../Resources/views', 'core');
        $this->registerValidationRules();
        //Register all routes defined by objects
        $this->app->booted(function () {
            \Routes::registerAll();
        });
    }

    /**
     * Extends Laravel validation with new rules
     */
    public function registerValidationRules()
    {
        \Validator::extend('valid_url', CoreValidationRules::class.'@validUrl');
    }

    /**
     * Adds some config to Javascript
     */
    public function registerJsConfig()
    {
        \JsConfig::setManyFromConfig(
            [
                'app.name',
                'app.env',
                'app.debug',
                'app.url',
                'core.ajaxPrefix',
                'core.adminPrefix'
            ]
        );
    }

    public function registerRouteMiddlewares(Router $router)
    {
        foreach ($this->routeMiddlewares as $name => $middleware) {
            $router->aliasMiddleware($name, $middleware);
        }
    }

    public function registerGlobalMiddlewares(Kernel $kernel)
    {
        foreach ($this->globalMiddlewares as $middleware) {
            $kernel->pushMiddleware($middleware);
        }
    }

    public function registerGroupMiddlewares(Router $router)
    {
        foreach ($this->groupMiddlewares as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $router->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }

    /**
     * Front end assets
     */
    public function registerAssets()
    {
        Asset::addVersioning();
        Asset::container('vendor')->add('js-manifest', 'manifest.js')
            ->add('js-vendor', 'vendor.js');
        Asset::container('modules')->add('core-js', 'module-assets/Core.js')
            ->add('core-css', 'module-assets/Core.css');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/core.php', 'core'
        );
        $this->replaceConfigFrom(
            __DIR__.'/../Config/modules.php', 'modules'
        );
        $this->publishes([
            __DIR__.'/../Config/core.php' => config_path('core.php')
        ], 'core-config');
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

}
