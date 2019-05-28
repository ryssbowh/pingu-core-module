<?php 
namespace Pingu\Core\Console;

use Pingu\Core\Facades\Theme;

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

        $themeJson = new \Pingu\Core\Components\themeManifest([
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

        $this->createComposerFile($themeName);
        $this->createWebpackFile($themeName, $assetPath);
        $this->createReadmeFile($themeName);
        $this->createConfigFile($themeName);
        $this->createPackageFile($themeName);
        $this->createComposeFile($themeName);

        $themeJson->saveToFile(themes_path($themeName."/theme.json"));

        // Rebuild Themes Cache
        Theme::rebuildCache();
        $this->info("Theme created !");
    }

    public function createComposerFile($name)
    {
        $content = file_get_contents(module_path('Core').'/stubs/themes/composer.stub');
        $search = ['$NAME', '$LOWERNAME'];
        $replace = [$name, strtolower($name)];
        $content = str_replace($search, $replace, $content);
        $this->files->put(themes_path($name).'/composer.json', $content);
    }

    public function createWebpackFile($themeName, $assetFolder)
    {
        $content = file_get_contents(module_path('Core').'/stubs/themes/theme.stub');
        $search = ['$NAME', '$ASSETFOLDER'];
        $replace = [$themeName, $assetFolder];
        $content = str_replace($search, $replace, $content);
        $this->files->put(themes_path($themeName).'/webpack.mix.js', $content);
    }

    public function createReadmeFile($themeName)
    {
        $this->files->put(themes_path($themeName).'/README.md', '');
    }

    public function createConfigFile($themeName)
    {
        $content = file_get_contents(module_path('Core').'/stubs/themes/config.stub');
        $this->files->put(themes_path($themeName).'/config.php', $content);
    }

    public function createPackageFile($themeName)
    {
        $content = file_get_contents(module_path('Core').'/stubs/themes/package.stub');
        $this->files->put(themes_path($themeName).'/package.json', $content);
    }

    public function createComposeFile($themeName)
    {
        $content = file_get_contents(module_path('Core').'/stubs/themes/composer_composers.stub');
        $content = str_replace('$NAME$', $themeName, $content);
        $this->files->put(themes_path($themeName).'/Composer.php', $content);
        exec('composer du');
    }

}
