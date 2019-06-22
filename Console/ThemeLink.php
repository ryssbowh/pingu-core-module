<?php

namespace Pingu\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class ThemeLink extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'theme:link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages themes sym links';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $themeName = $this->argument('theme');
        $deleting = $this->option('delete');
        if (!$themeName) {
            $themes = \Theme::all();
        }
        else{
            $themes = [\Theme::find($themeName)];
        }

        foreach($themes as $theme){
            $publicDirectory = public_path(config('core.themes.public_path'));
            $publicLink = $publicDirectory.'/'.$theme->name;
            if($deleting){
                $this->deleteLink($publicLink);
            }
            else{
                $publicThemeFolder = themes_path($theme->name.'/public');
                $this->createLink($publicThemeFolder, $publicDirectory, $publicLink);
            }
        }
    }

    protected function createLink($publicThemeFolder, $publicDirectory, $publicLink)
    {
        if(!file_exists($publicThemeFolder)){
            \File::makeDirectory($publicThemeFolder);
        }
        if(!file_exists($publicDirectory)){
            \File::makeDirectory($publicDirectory);
        }
        if(file_exists($publicLink)){
            \File::delete($publicLink);
        }
        \File::link($publicThemeFolder, $publicLink);
        $this->info("Created link ".$publicLink.' -> '.$publicThemeFolder);
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
            ['theme', InputArgument::OPTIONAL, 'The theme name'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['delete', 'd', InputOption::VALUE_NONE, 'Delete the link'],
        ];
    }
}
