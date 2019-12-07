<?php 
namespace Pingu\Core\Theming;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\View\FileViewFinder;
use Pingu\Core\Exceptions\themeNotFound;
use Pingu\Core\Facades\Theme;

class ThemeViewFinder extends FileViewFinder
{
    public function __construct(Filesystem $files, array $paths, array $extensions = null)
    {
        $this->themeEngine = \App::make('core.themes');
        parent::__construct($files, $paths, $extensions);
    }

    /*
     * Override findNamespacedView() to add "Theme/vendor/..." paths
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        // Extract the $view and the $namespace parts
        list($namespace, $view) = $this->parseNamespaceSegments($name);

        $paths = $this->addThemeNamespacePaths($namespace);

        // Find and return the view
        return $this->findInPaths($view, $paths);
    }

    public function addThemeNamespacePaths($namespace)
    {
        if (!isset($this->hints[$namespace])) {
            return [];
        }

        $paths = $this->hints[$namespace];
        // try{
            $themePaths = Theme::getViewPaths();
        // }
        // catch(themeNotFound $e){
        //     return $paths;
        // }

        $newPaths = [];
        foreach($paths as $path){
            $pathRelativeToApp = substr($path, strlen(base_path()) + 1);
            if (strpos($pathRelativeToApp, 'Modules') === 0) {
                $newPath = '/'.config('core.themes.modules_namespaced_views').'/'.$namespace;
            }
            elseif (strpos($pathRelativeToApp, 'vendor') === 0) {
                $newPath = '/vendor/'.$namespace;
            }
            else{
                continue;
            }

            foreach($themePaths as $themePath){
                $newPaths[] = $themePath.$newPath;
            }
        }
        return array_merge($newPaths, $paths);
    }

    /**
     * Override replaceNamespace() to add path for custom error pages "Theme/errors/..."
     *
     * @param  string       $namespace
     * @param  string|array $hints
     * @return void
     */
    public function replaceNamespace($namespace, $hints)
    {
        $this->hints[$namespace] = (array) $hints;

        // Overide Error Pages
        if ($namespace == 'errors' || $namespace == 'mails') {

            $searchPaths = array_diff($this->paths, Theme::getLaravelViewPaths());

            $addPaths = array_map(
                function ($path) use ($namespace) {
                    return "$path/$namespace";
                }, $searchPaths
            );

            $this->prependNamespace($namespace, $addPaths);
        }
    }

    /**
     * Set the array of paths where the views are being searched.
     *
     * @param array $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
        $this->flush();
    }

    /**
     * Get the array of paths wherew the views are being searched.
     */
    public function getPaths()
    {
        return $this->paths;
    }

}
