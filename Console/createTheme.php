<?php 
namespace Modules\Core\Console;

use Modules\Core\Facades\Theme;

class createTheme extends baseThemeCommand
{
    protected $signature = 'theme:create {themeName?}';
    protected $description = 'Create a new theme';

    public function info($text, $newline = true)
    {
        $this->output->write("<info>$text</info>", $newline);
    }

    public function handle()
    {
        // Get theme name
        $themeName = $this->argument('themeName');
        if (!$themeName) {
            $themeName = $this->ask('Give theme name');
        }

        // Check that theme doesn't exist
        if ($this->theme_installed($themeName)) {
            $this->error("Error: Theme $themeName already exists");
            return;
        }

        $viewsPath = $this->ask('Views folder', config('core.themes.views_path'));
        $assetPath = $this->ask('Assets folder', config('core.themes.asset_path'));
        $imagesPath = $this->ask('Images folder', config('core.themes.images_path'));

        $viewsPathFull = themes_path($themeName.'/'.$viewsPath);
        $assetPathFull = themes_path($themeName.'/'.$assetPath);
        $imagePathFull = themes_path($themeName.'/'.$imagesPath);

        // Ask for parent theme
        $parentTheme = "";
        if ($this->confirm('Extends an other theme?')) {
            $themes = array_map(function ($theme) {
                return $theme->name;
            }, Theme::all());
            $parentTheme = $this->choice('Which one', $themes);
        }

        $themeJson = new \Modules\Core\Components\themeManifest([
            "name" => $themeName,
            "extends" => $parentTheme,
            "views-path" => $viewsPath,
            "asset-path" => $assetPath,
            "images-path" => $imagesPath,
        ]);

        // Create Paths + copy theme.json
        $this->files->makeDirectory(themes_path($themeName));
        $this->files->makeDirectory($viewsPathFull);
        $this->files->makeDirectory($assetPathFull);
        $this->files->makeDirectory($assetPathFull.'/css');
        $this->files->makeDirectory($assetPathFull.'/js');
        $this->files->put($assetPathFull.'/js/app.js','');
        $this->files->put($assetPathFull.'/css/master.scss','');
        $this->files->makeDirectory($imagePathFull);

        $this->createWebpackFile($themeName, $assetPath);

        $themeJson->saveToFile(themes_path($themeName."/theme.json"));

        // Rebuild Themes Cache
        Theme::rebuildCache();
    }

    public function createWebpackFile($themeName, $assetFolder)
    {
        $content = "
const mix = require('laravel-mix');
const path = require('path');

var assetPath = './public/themes/".$themeName."/".$assetFolder."/';

//Javascript
mix.js(assetPath + 'js/app.js', assetPath + '".$themeName.".js').sourceMaps();

//Css
mix.sass(assetPath + 'css/master.scss', assetPath + '".$themeName.".css');
";
        $this->files->put(themes_path($themeName).'/webpack.mix.js', $content);
    }

}
