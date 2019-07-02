<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Nwidart\Modules\Commands\ModuleMakeCommand;
use Nwidart\Modules\Generators\ModuleGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeModule extends ModuleMakeCommand
{

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        with(new ModuleGenerator($name))
            ->setFilesystem($this->laravel['files'])
            ->setModule($this->laravel['modules'])
            ->setConfig($this->laravel['config'])
            ->setConsole($this)
            ->setForce($this->option('force'))
            ->setPlain($this->option('plain'))
            ->generate();

        \Event::dispatch('modules.created', $name);
    }

    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the module.'],
        ];
    }
}
