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
            $themeName = $this->ask('Give theme name:');
        }

        // Check that theme doesn't exist
        if ($this->theme_installed($themeName)) {
            $this->error("Error: Theme $themeName already exists");
            return;
        }

        // Read theme paths
        $viewsPath = 'views';

        $viewsPathFull = public_path($themeName.'/views');
        $assetPathFull = public_path($themeName.'/assets');
        $imagePathFull = public_path($themeName.'/images');

        // Ask for parent theme
        $parentTheme = "";
        if ($this->confirm('Extends an other theme?')) {
            $themes = array_map(function ($theme) {
                return $theme->name;
            }, Theme::all());
            $parentTheme = $this->choice('Which one', $themes);
        }

        $customConfiguration = $this->askCustomConfiguration();

        if (!empty($customConfiguration)) {
            $this->info("Custom Theme Configuration:");
            foreach ($customConfiguration as $key => $value) {
                $this->info("- $key: " . print_r($value, true));
            }
        }

        $themeJson = new \Modules\Core\Components\themeManifest(array_merge([
            "name" => $themeName,
            "extends" => $parentTheme,
        ], $customConfiguration));

        // Create Paths + copy theme.json
        $this->files->makeDirectory($viewsPathFull);
        $this->files->makeDirectory($assetPathFull);
        $this->files->makeDirectory($imagePathFull);

        $themeJson->saveToFile(themes_path("$viewsPath/theme.json"));

        // Rebuild Themes Cache
        Theme::rebuildCache();
    }

    // You request more information during theme setup. Just override this class and implement
    // the following method. It should return an associative array which will be appended
    // into the 'theme.json' configuration file. You can retreive this values
    // with Theme::getSetting('key') at runtime. You may optionaly want to redifine the
    // command signature too.
    public function askCustomConfiguration()
    {
        return [
            // 'key' => 'value',
        ];
    }

}
