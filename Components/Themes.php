<?php 

namespace Pingu\Core\Components;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Pingu\Core\Exceptions\{themeAlreadyExists, themeNotFound};
use ThemeConfig, View;

class Themes
{
    protected $themesPath;
    protected $activeTheme = null;
    protected $themes = [];
    protected $laravelViewsPath;
    protected $cachePath;

    public function __construct()
    {
        $this->laravelViewsPath = config('view.paths');
        $this->themesFolder = 'Themes';
        $this->themesPath = base_path($this->themesFolder);
        $this->cachePath = base_path('bootstrap/cache/themes.php');
    }

    /**
     * Return $filename path located in themes folder
     *
     * @param  string $filename
     * @return string
     */
    public function themes_path($filename = null)
    {
        return $filename ? $this->themesPath . '/' . $filename : $this->themesPath;
    }

    /**
     * Return list of registered themes
     *
     * @return array
     */
    public function all()
    {
        return $this->themes;
    }

    /**
     * Check if @themeName is registered
     *
     * @return bool
     */
    public function exists($themeName)
    {
        foreach ($this->themes as $theme) {
            if ($theme->name == $themeName) {
                return true;
            }

        }
        return false;
    }

    /**
     * set a theme given a request
     * @param  Illuminate\Http\Request $request
     * @return ?Theme
     */
    public function setByRequest(Request $request)
    {
        $setting = 'core.frontTheme';

        if($request->ajax()){
            $params = $request->all();
            if(isset($params['_theme'])){
                if($params['_theme'] == 'admin') $setting = 'core.adminTheme';
            }
            else{
                //ajax call doesn't set a theme, aborting
                return null;
            }
        }
        else{
            $segments = $request->segments();
            if(isset($segments[0]) and $segments[0] == 'admin'){
                $setting = 'core.adminTheme';
            }
        }

        return $this->setByName(config($setting), !$request->wantsJson());
    }

    public function setFront()
    {
        $front = config('core.frontTheme', false);
        if($front){
            $this->setByName($front);
        }
    }

    /**
     * Set theme by its name
     * @param string $themeName
     * @return Theme
     */
    public function setByName(?string $themeName, $setAssets = true)
    {
        if(is_null($themeName)) return;

        if ($this->exists($themeName)) {
            $theme = $this->find($themeName);
        } else {
            throw new themeNotFound($themeName." isn't a valid theme");
        }

        $this->activeTheme = $theme;

        // set theme view paths
        $paths = array_merge(config('view.paths'),$theme->getViewPaths());
        config(['view.paths' => $paths]);
        app('view.finder')->setPaths($paths);

        //set theme config
        $config = (include $theme->getPath('config.php'));
        ThemeConfig::setConfig($config);

        //register the theme assets
        if($setAssets){
            \Asset::container('theme')->add('css', 'theme-assets/'.$theme->name.'.css');
            \Asset::container('theme')->add('js', 'theme-assets/'.$theme->name.'.js');
        }

        // registers theme composers
        $composersClass = "Pingu\\Themes\\".$theme->name."\\Composer";
        View::composers($composersClass::getComposers());

        Event::dispatch('core.theme.change', $theme);
        return $theme;
    }

    public function front()
    {
        return $this->find(config('core.frontTheme'));
    }

    /**
     * Get current theme
     *
     * @return Theme
     */
    public function current()
    {
        return $this->activeTheme ? $this->activeTheme : null;
    }

    /**
     * Get current theme's name
     *
     * @return string
     */
    public function get()
    {
        return $this->current() ? $this->current()->name : '';
    }

    /**
     * Find a theme by it's name
     *
     * @return Theme
     */
    public function find($themeName)
    {
        // Search for registered themes
        foreach ($this->themes as $theme) {
            if ($theme->name == $themeName) {
                return $theme;
            }

        }

        throw new themeNotFound("Theme $themeName not found");
    }

    /**
     * Register a new theme
     *
     * @return Theme
     */
    public function add(Theme $theme)
    {
        if ($this->exists($theme->name)) {
            throw new themeAlreadyExists($theme);
        }
        $this->themes[] = $theme;
        return $theme;
    }

    public function getViewPaths()
    {
        if($theme = $this->current()){
            return $theme->getViewPaths();
        }
        return [];
    }

    // Original view paths defined in config.view.php
    public function getLaravelViewPaths()
    {
        return $this->laravelViewsPath;
    }

    public function cacheEnabled()
    {
        return config('core.themes.cache', false);
    }

    // Rebuilds the cache file
    public function rebuildCache()
    {
        $themes = $this->scanJsonFiles();
        file_put_contents($this->cachePath, json_encode($themes, JSON_PRETTY_PRINT));
        $stub = file_get_contents(realpath(__DIR__ . '/../stubs/theme_cache.stub'));
        $contents = str_replace('[CACHE]', var_export($themes, true), $stub);
        file_put_contents($this->cachePath, $contents);
    }

    // Loads themes from the cache
    public function loadCache()
    {
        if (!file_exists($this->cachePath)) {
            $this->rebuildCache();
        }

        // $data = json_decode(file_get_contents($this->cachePath), true);

        $data = include $this->cachePath;

        if ($data === null) {
            throw new \Exception("Invalid theme cache json file [{$this->cachePath}]");
        }
        return $data;
    }

    // Scans theme folders for theme.json files and returns an array of themes
    public function scanJsonFiles()
    {
        $themes = [];
        foreach (glob($this->themes_path('*'), GLOB_ONLYDIR) as $themeFolder) {
            $themeFolder = realpath($themeFolder);
            if (file_exists($jsonFilename = $themeFolder . '/' . 'theme.json')) {

                $folders = explode(DIRECTORY_SEPARATOR, $themeFolder);
                $themeName = end($folders);

                // default theme settings
                $defaults = [
                    'name'       => $themeName,
                    'asset-path' => config('core.theme.asset_path'),
                    'views-path' => config('core.theme.views_path'),
                    'extends'    => null
                ];

                // If theme.json is not an empty file parse json values
                $json = file_get_contents($jsonFilename);
                if ($json !== "") {
                    $data = json_decode($json, true);
                    if ($data === null) {
                        throw new \Exception("Invalid theme.json file at [$themeFolder]");
                    }
                } else {
                    $data = [];
                }

                $themes[] = array_merge($defaults, $data);
            }
        }
        return $themes;
    }

    public function loadThemesJson()
    {
        if ($this->cacheEnabled()) {
            return $this->loadCache();
        } else {
            return $this->scanJsonFiles();
        }
    }

    /**
     * Scan all folders inside the themes path & config/themes.php
     * If a "theme.json" file is found then load it and setup theme
     */
    public function scanThemes()
    {
        $this->themes = [];
        $parentThemes = [];

        foreach ($this->loadThemesJson() as $data) {
            // Create theme
            $theme = new Theme(
                $data['name'],
                $data['asset-path'],
                $data['views-path']
            );

            // Has a parent theme? Store parent name to resolve later.
            if ($data['extends']) {
                $parentThemes[$theme->name] = $data['extends'];
            }

            // Load the rest of the values as theme Settings
            $theme->loadSettings($data);
        }

        // All themes are loaded. Now we can assign the parents to the child-themes
        foreach ($parentThemes as $childName => $parentName) {
            $child = $this->find($childName);

            if ($this->exists($parentName)) {
                $parent = $this->find($parentName);
            } else {
                $parent = new Theme($parentName);
            }

            $child->setParent($parent);
        }
    }

    /*--------------------------------------------------------------------------
    | Proxy to current theme
    |--------------------------------------------------------------------------*/

    // Return url of current theme
    public function url($filename)
    {
        // If no Theme set, return /$filename
        if (!$this->current()) {
            return "/" . ltrim($filename, '/');
        }

        return $this->current()->url($filename);
    }

    /**
     * Act as a proxy to the current theme. Map theme's functions to the Themes class. (Decorator Pattern)
     */
    public function __call($method, $args)
    {
        if (($theme = $this->current())) {
            return call_user_func_array([$theme, $method], $args);
        } else {
            throw new themeNotFound("No theme is set. Can not execute method [$method] in [" . self::class . "]", 1);
        }
    }

    /*--------------------------------------------------------------------------
    | Blade Helper Functions
    |--------------------------------------------------------------------------*/

    /**
     * Return css link for $href
     *
     * @param  string $href
     * @return string
     */
    public function css($href)
    {
        return sprintf('<link media="all" type="text/css" rel="stylesheet" href="%s">', $this->url($href));
    }

    /**
     * Return script link for $href
     *
     * @param  string $href
     * @return string
     */
    public function js($href)
    {
        return sprintf('<script src="%s"></script>', $this->url($href));
    }

    /**
     * Return img tag
     *
     * @param  string $src
     * @param  string $alt
     * @param  string $Class
     * @param  array $attributes
     * @return string
     */
    public function img($src, $alt = '', $class = '', $attributes = [])
    {
        return sprintf('<img src="%s" alt="%s" class="%s" %s>',
            $this->url($src),
            $alt,
            $class,
            $this->HtmlAttributes($attributes)
        );
    }

    /**
     * Return attributes in html format
     *
     * @param  array $attributes
     * @return string
     */
    private function HtmlAttributes($attributes)
    {
        $formatted = join(' ', array_map(function ($key) use ($attributes) {
            if (is_bool($attributes[$key])) {
                return $attributes[$key] ? $key : '';
            }
            return $key . '="' . $attributes[$key] . '"';
        }, array_keys($attributes)));
        return $formatted;
    }

}
