<?php

namespace Pingu\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Pingu\Core\Seeding\SeederMigrationCreator;
use Pingu\Core\Seeding\SeederMigrator;
use Pingu\Core\Seeding\SeederRepository;

class SeederServiceProvider extends ServiceProvider
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
            SeederRepository::class, function ($app) {
                return new SeederRepository($app['db'], config('core.seeders.table'));
            }
        );
        $this->app->singleton(
            SeederMigrator::class, function ($app) {
                return new SeederMigrator($app[SeederRepository::class], $app['db'], $app['files']);
            }
        );
        $this->app->singleton(
            SeederMigrationCreator::class, function ($app) {
                return new SeederMigrationCreator($app['files']);
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
            SeederRepository::class,
            SeederMigrator::class,
            SeederMigrationCreator::class
        ];
    }

}
