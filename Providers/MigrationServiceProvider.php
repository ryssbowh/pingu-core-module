<?php

namespace Pingu\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Pingu\Core\Migrations\MigrationCreator;

class MigrationServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(
            'migration.creator', function ($app) {
                return new MigrationCreator($app['files']);
            }
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [
            'migration.creator'
        ];
    }

}
