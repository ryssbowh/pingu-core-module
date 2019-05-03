<?php 
namespace Modules\Core\Components;

use Igaster\LaravelTheme\Facades\Theme;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\View\FileViewFinder;
use Igaster\LaravelTheme\themeViewFinder as themeViewFinderIgaster ;

class themeViewFinder2 extends themeViewFinderIgaster
{

    public function addThemeNamespacePaths($namespace)
    {
        // This rule will remap all paths starting with $key to $value.
        // For exapmle paths starting with 'resources/views/vendor' (relative to base_path())
        // will be maped to path 'THEMENAME/vendor' (relative to current theme views-path)
        $pathsMap = [
            // 'resources/views/vendor/mail' => 'mail',
            'resources/views/vendor' => 'vendor',
            'resources/views/modules' => 'modules',
        ];

        // Does $namespace exists?
        if (!isset($this->hints[$namespace])) {
            return [];
        }

        // Get the paths registered to the $namespace
        $paths = $this->hints[$namespace];

        // Search $paths array and remap paths that start with a key of $pathsMap array.
        // replace with the value of $pathsMap array
        $themeSubPaths = [];
        $newPaths = [];
        foreach ($paths as $path) {
            $pathRelativeToApp = substr($path, strlen(base_path()) + 1);
            // Ignore paths in composer installed packages (paths inside vendor folder)
            if (strpos($pathRelativeToApp, 'vendor') !== 0) {
                // Remap paths definded int $pathsMap array
                $found = false;
                foreach ($pathsMap as $key => $value) {
                    if (strpos($pathRelativeToApp, $key) === 0) {
                        $pathRelativeToApp = str_replace($key, $value, $pathRelativeToApp);
                        $themeSubPaths[] = $pathRelativeToApp;
                        $found = true;
                        break;
                    }
                }
                if(!$found) $newPaths[] = $path;
            }
            else{
                $newPaths[] = $path;
            }
        }

        // Prepend current theme's view path to the remaped paths
        $newThemePaths = [];
        $searchPaths = array_diff($this->paths, Theme::getLaravelViewPaths());
        foreach ($searchPaths as $path1) {
            foreach ($themeSubPaths as $path2) {
                $newThemePaths[] = $path1 . '/' . $path2;
            }
        }

        $paths = array_merge($newThemePaths, $newPaths);

        return $paths;
    }

    public function getThemesPublishPaths(string $name){
        $out = [];
        foreach(Theme::all() as $theme){
            $out[resource_path('views').'/'.$theme->name.'/modules/'.$name] = 'views_'.$name.'_'.$theme->name;
        }
        return $out;
    }
}
