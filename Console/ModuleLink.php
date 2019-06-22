<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ModuleLink extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages modules sym links';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $moduleName = $this->argument('module');
        $deleting = $this->option('delete');
        if(!$moduleName) {
            $modules = \Module::all();
        }
        else{
            $module = \Module::findOrFail($moduleName);
            $modules = [$module];
        }
        foreach($modules as $module){
            $publicDirectory = public_path(config('modules.public_path'));
            $publicLink = $publicDirectory.'/'.$module->getName();
            if($deleting){
                $this->deleteLink($publicLink);
            }
            else{
                $publicModuleFolder = $module->getPath().'/public';
                $this->createLink($publicModuleFolder, $publicDirectory, $publicLink);
            }
        }
    }

    protected function createLink($publicModuleFolder, $publicDirectory, $publicLink)
    {
        if(!file_exists($publicModuleFolder)){
            \File::makeDirectory($publicModuleFolder);
        }
        if(!file_exists($publicDirectory)){
            \File::makeDirectory($publicDirectory);
        }
        if(file_exists($publicLink)){
            \File::delete($publicLink);
        }
        \File::link($publicModuleFolder, $publicLink);
        $this->info("Created link ".$publicLink.' -> '.$publicModuleFolder);
    }

    public function deleteLink($publicLink)
    {
        if(file_exists($publicLink)){
            \File::delete($publicLink);
        }
        $this->info("Deleted link ".$publicLink);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::OPTIONAL, 'Name of module'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['delete', 'd', InputOption::VALUE_NONE, 'Delete the link'],
        ];
    }
}
