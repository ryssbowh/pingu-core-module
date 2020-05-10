<?php

namespace Pingu\Core\Providers;

use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;
use Pingu\Core\Console\BuildAssets;
use Pingu\Core\Console\GenerateDoc;
use Pingu\Core\Console\MakeComposer;
use Pingu\Core\Console\MergePackages;
use Pingu\Core\Console\ModuleLink;
use Pingu\Core\Console\ModuleMakeSettings;
use Pingu\Core\Console\SeedInstall;
use Pingu\Core\Console\SeedMake;
use Pingu\Core\Console\SeedRollback;
use Pingu\Core\Console\SeedRun;
use Pingu\Core\Console\ThemeLink;
use Pingu\Core\Seeding\SeederMigrationCreator;
use Pingu\Core\Seeding\SeederMigrator;
use Pingu\Core\Seeding\SeederRepository;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    protected $commands = [
        'command.doc',
        'command.moduleLink',
        'command.assets',
        
        'command.makeSettings'
    ];
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCommands();
    }
    /**
     * Registers the serve command
     */
    protected function registerCommands()
    {
        $this->app->bind(
            'command.doc', function ($app) {
                return new GenerateDoc();
            }
        );
        $this->app->bind(
            'command.moduleLink', function ($app) {
                return new ModuleLink();
            }
        );
        $this->app->bind(
            'command.assets', function ($app) {
                return new BuildAssets();
            }
        );
        
        $this->app->bind(
            'command.makeSettings', function ($app) {
                return new ModuleMakeSettings($app['files']);
            }
        );
        
        $this->commands($this->commands);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return $this->commands;
    }
}