<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class GenerateDoc extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:generate-doc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates documentation with php documentor';

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
        if($name = $this->argument('module')){
            $module = \Module::findOrFail($name);
            $modules = [$module];
        }
        else{
            $modules = \Module::all();
        }
        $phar = module_path('Core').'/phpDocumentor.phar';
        foreach($modules as $module){
            if(!file_exists($module->getPath().'/phpdoc.dist.xml')){
                $this->warn("phpdoc.dist.xml not found for module ".$module->getName());
                continue;
            }
            echo "Generating for {$module->getPath()}...\n";
            exec('cd '.$module->getPath()." && php $phar", $output);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::OPTIONAL, 'Module name.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
