<?php 
namespace Pingu\Core\Theming;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\View\FileViewFinder;
use Pingu\Core\Exceptions\themeNotFound;
use Pingu\Core\Facades\Theme;
use InvalidArgumentException;

class ThemeViewFinder extends FileViewFinder
{
    const MODULE_PATH_DELIMITER = '@';

    protected $moduleHints = [];

    public function __construct(Filesystem $files, array $paths, array $hints, array $extensions = null)
    {
        $this->hints = $hints;
        $this->themeEngine = \App::make('core.themes');
        parent::__construct($files, $paths, $extensions);
    }

    public function find($name)
    {
        if (isset($this->views[$name])) {
            return $this->views[$name];
        }

        if ($this->hasModuleInformation($name = trim($name))) {
            return $this->views[$name] = $this->findModuleView($name);
        }

        if ($this->hasHintInformation($name = trim($name))) {
            return $this->views[$name] = $this->findNamespacedView($name);
        }

        return $this->views[$name] = $this->findInPaths($name, $this->paths);
    }

    public function addModuleNamespace($namespace, $hints)
    {
        $hints = (array) $hints;

        if (isset($this->hints[$namespace])) {
            $hints = array_merge($this->moduleHints[$namespace], $hints);
        }

        $this->moduleHints[$namespace] = $hints;
    }

    protected function parseModuleSegments($name)
    {
        $segments = explode(static::MODULE_PATH_DELIMITER, $name);

        if (count($segments) !== 2) {
            throw new InvalidArgumentException("View [{$name}] has an invalid name.");
        }

        if (! isset($this->moduleHints[$segments[0]])) {
            throw new InvalidArgumentException("No module hint path defined for [{$segments[0]}].");
        }

        return $segments;
    }

    public function hasModuleInformation($name)
    {
        return strpos($name, static::MODULE_PATH_DELIMITER) > 0;
    }

    protected function findModuleView($name)
    {
        [$namespace, $view] = $this->parseModuleSegments($name);

        return $this->findInPaths($view, $this->moduleHints[$namespace]);
    }

    public function addThemeModulePaths($themeViewPaths)
    {
        foreach ($this->moduleHints as $namespace => $paths) {
            foreach (array_reverse($themeViewPaths) as $themeViewPath) {
                $newPath = $themeViewPath.'/'.config('core.themes.modules_namespaced_views').'/'.$namespace;
                if (is_dir($newPath)) {
                    $this->moduleHints[$namespace] = array_unique(array_merge([$newPath], $this->moduleHints[$namespace]));
                }
            }
        }
    }

    public function addThemeVendorPath($themeViewPaths)
    {
        foreach ($this->hints as $namespace => $paths) {
            foreach (array_reverse($themeViewPaths) as $themeViewPath) {
                $newPath = $themeViewPath.'/'.config('core.themes.vendor_namespaced_views').'/'.$namespace;
                if (is_dir($newPath)) {
                    $this->hints[$namespace] = array_unique(array_merge([$newPath], $this->hints[$namespace]));
                }
            }
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

}
