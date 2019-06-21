<?php 
namespace Pingu\Core\Components;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Pingu\Core\Exceptions\themeException;

class Theme
{
    public $name;
    public $viewsPath;
    public $assetPath;
    public $imagesPath;
    public $settings = [];

    /** @var Theme  */
    public $parent;

    /** @var \Pingu\Core\Components\Themes */
    private $themes;

    public function __construct($themeName, $assetPath = null, $viewsPath = null, $imagesPath = null, Theme $parent = null)
    {
        $this->themes = resolve('core.themes');

        $this->name = $themeName;
        $this->assetPath = ($assetPath === null ? config('core.themes.asset_path') : $assetPath);
        $this->viewsPath = ($viewsPath === null ? config('core.themes.views_path') : $viewsPath);
        $this->imagesPath = ($imagesPath === null ? config('core.themes.images_path') : $imagesPath);
        $this->parent = $parent;

        $this->themes->add($this);
    }

    public function getViewPaths()
    {
        // Build Paths array.
        // All paths are relative to Config::get('theme.theme_path')
        $paths = [];
        $theme = $this;
        do {
            $path = $theme->getPath().$theme->viewsPath;
            
            if (!in_array($path, $paths)) {
                $paths[] = $path;
            }

        } while ($theme = $theme->parent);

        return $paths;
    }

    public function url($url)
    {
        $url = ltrim($url, '/');
        // return external URLs unmodified
        if (preg_match('/^((http(s?):)?\/\/)/i', $url)) {
            return $url;
        }

        // Is theme folder located on the web (ie AWS)? Dont lookup parent themes...
        if (preg_match('/^((http(s?):)?\/\/)/i', $this->assetPath)) {
            return $this->assetPath . '/' . $url;
        }

        // Check for valid {xxx} keys and replace them with the Theme's configuration value (in themes.php)
        preg_match_all('/\{(.*?)\}/', $url, $matches);
        foreach ($matches[1] as $param) {
            if (($value = $this->getSetting($param)) !== null) {
                $url = str_replace('{' . $param . '}', $value, $url);
            }
        }

        // Seperate url from url queries
        if (($position = strpos($url, '?')) !== false) {
            $baseUrl = substr($url, 0, $position);
            $params = substr($url, $position); 
        } else {
            $baseUrl = $url;
            $params = '';
        }

        // Lookup asset in current's theme asset path
        $fullUrl = '/themes/' . $this->name . '/' . $baseUrl;

        // dump($fullUrl);

        if (file_exists($fullPath = public_path($fullUrl))) {
            return $fullUrl . $params;
        }

        // If not found then lookup in parent's theme asset path
        if ($parentTheme = $this->getParent()) {
            return $parentTheme->url($url);
        }
        // No parent theme? Lookup in the public folder.
        else {
            if (file_exists(public_path($baseUrl))) {
                return "/" . $baseUrl . $params;
            }
        }

        // Asset not found at all. Error handling
        $action = Config::get('core.themes.asset_not_found', 'LOG_ERROR');

        if ($action == 'THROW_EXCEPTION') {
            throw new themeException("Asset not found [$url]");
        } elseif ($action == 'LOG_ERROR') {
            Log::warning("Asset not found [$url] in Theme [" . $this->themes->current()->name . "]");
        } else {
            // themes.asset_not_found = 'IGNORE'
            return '/' . $url;
        }
    }

    public function getPath($sub = '')
    {
        return themes_path($this->name.'/'.$sub);
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent(Theme $parent)
    {
        $this->parent = $parent;
    }

    public function install($clearPaths = false)
    {
        $viewsPath = $this->getPath($this->viewsPath);
        $assetPath = $this->getPath($this->assetPath);

        if ($clearPaths) {
            if (File::exists($viewsPath)) {
                File::deleteDirectory($viewsPath);
            }
            if (File::exists($assetPath)) {
                File::deleteDirectory($assetPath);
            }
        }

        File::makeDirectory($viewsPath);
        File::makeDirectory($assetPath);

        $themeJson = new \Pingu\Core\Components\themeManifest(array_merge($this->settings, [
            'name' => $this->name,
            'extends' => $this->parent ? $this->parent->name : null,
            'asset-path' => $this->assetPath,
            'view-path' => $this->viewsPath,
        ]));
        $themeJson->saveToFile($this->getPath()."/theme.json");

        $this->themes->rebuildCache();
    }

    /*--------------------------------------------------------------------------
    | Theme Settings
    |--------------------------------------------------------------------------*/

    public function setSetting($key, $value)
    {
        $this->settings[$key] = $value;
    }

    public function getSetting($key, $default = null)
    {
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        } elseif ($parent = $this->getParent()) {
            return $parent->getSetting($key, $default);
        } else {
            return $default;
        }
    }

    public function loadSettings($settings = [])
    {
        $this->settings = array_diff_key((array) $settings, array_flip([
            'name',
            'extends',
            'views-path',
            'asset-path'
        ]));

    }

}
