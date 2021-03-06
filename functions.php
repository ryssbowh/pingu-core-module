<?php

use Illuminate\Support\Str;
use Pingu\Core\Exceptions\RouteNameDoesNotExistsException;

/*
|--------------------------------------------------------------------------
| Register Functions
|--------------------------------------------------------------------------
|
| When your module starts, this file is executed automatically.
| This is a good place to register functions
|
*/

if (! function_exists('friendly_field_name')) {
    /**
     * Friendly name for a field
     * 
     * @param string $name
     * 
     * @return string
     */
    function friendly_field_name($name): string
    {
        return Str::title(str_replace('_', ' ', $name));
    }
}

if (! function_exists('explode_camel_case')) {
    /**
     * Explodes a string by camel case
     * 
     * @param  strign $str
     * @return string
     */
    function explode_camel_case($str): string
    {
        return trim(implode(' ', preg_split('/(?=[A-Z])/', $str)));
    }
}

if (! function_exists('friendly_classname')) {
    /**
     * Takes a fully namespaced class and returns its name, exploded by camel case
     * 
     * @param  string $str
     * @return string
     */
    function friendly_classname($class): string
    {
        $str = object_to_class($class);
        return explode_camel_case(class_basename($str));
    }
}

if (! function_exists('themes_path')) {
    /**
     * Returns the path of a given file for the current theme
     * 
     * @param  string $filename
     * @return string|null
     */
    function themes_path($filename = null): ?string
    {
        return app()->make('core.themes')->themes_path($filename);
    }
}

if (! function_exists('theme_url')) {
    /**
     * Returns the url of a given resource for the current theme
     * 
     * @param  string $url
     * @return string|null
     */
    function theme_url($url): string
    {
        return app()->make('core.themes')->url($url);
    }
}

if (! function_exists('route_exists')) {
    /**
     * Checks if a route exists as a GET action. Checks path and name
     *
     * @param  string $uri
     * @return bool
     */
    function route_exists($uri): bool
    {
        if (!is_string($uri)) { return false;
        }
        $uri = trim($uri, '/');
        $routes = \Route::getRoutes()->getRoutes();
        foreach ($routes as $r) {
            if (($r->uri() == $uri or $uri == $r->getName()) and in_array('GET', $r->methods())) {
                return true;
            }
        }
        return false;
    }
}

if (! function_exists('route_by_name')) {
    /**
     * Search a route by its name
     * 
     * @param  string $name 
     * @return Route
     * 
     * @throws RouteNameDoesNotExistsException
     */
    function route_by_name(string $name)
    {
        if (!$route = \Route::getRoutes()->getByName($name)) {
            throw new RouteNameDoesNotExistsException("Route ".$name." doesn't exists");
        }
        return $route;
    }
}

if (! function_exists('routes_with_friendly_name')) {
    /**
     * Returns all routes that have a friendly name.
     * Routes that have no names will be excluded
     * 
     * @return array
     */
    function routes_with_friendly_name()
    {
        $routes = [];
        foreach (app('router')->getRoutes()->getIterator() as $route) {
            if ($friendly = $route->getAction('friendly') and $route->getName()) {
                $routes[$route->getName()] = $friendly;
            }
        }
        return $routes;
    }
}

if (! function_exists('theme_config')) {
    /**
     * Gets a theme config. If $value is not found, will return normal config
     * or the default if not found
     *
     * @param  mixed $value
     * @param  mixed $default
     * @return mixed
     */
    function theme_config($value, $default = null)
    {
        if(is_array($value)) {
            return ThemeConfig::set($value);
        }
        return ThemeConfig::get($value, $default);
    }
}

if (! function_exists('replaceUriSlugs')) {
    /**
     * Takes an uri ex /admin/{menu}/{item} and an array of object for replacements.
     * Will replace the slugs by the route key name for each object in the array, by order of appearance.
     * 
     * @param  string $uri
     * @param  array  $replacements
     * @return string
     */
    function replaceUriSlugs(string $uri, array $replacements): string
    {
        preg_match_all("/(?:\G(?!^)|)(\{[\w\-]+\})/", $uri, $matches);
        $matches = $matches[0];
        foreach ($matches as $i => $match) {
            $replacement = $replacements[$i] ?? $match;
            if (is_object($replacement)) {
                $replacement = $replacement->getRouteKey();
            }
            $uri = str_replace($match, $replacement, $uri);
        }
        return '/'.trim($uri, '/');
    }
}
 
if (! function_exists('upload_max_filesize')) {   
    /**
     * upload_max_filesize in Kb
     */
    function upload_max_filesize(): int
    {
        return rtrim(ini_get('upload_max_filesize'), 'M')*1000;
    }
}
   
if (! function_exists('temp_path')) { 
    /**
     * Temporary disk file path helper
     * 
     * @param  ?string $file
     * @return string
     */
    function temp_path($file = null): string
    {
        $path = config('filesystems.disks.tmp.root');
        if ($file) {
            $path .= DIRECTORY_SEPARATOR.$file;
        }
        return $path;
    }
}

if (! function_exists('namespace_from_file')) {
    /**
     * Extract namespace from a filename
     * 
     * @param  string $file
     * @return ?string
     */
    function namespace_from_file($file): ?string
    {
        $src = file_get_contents($file);
        if (preg_match('#^namespace\s+(.+?);$#sm', $src, $m)) {
            return $m[1];
        }
        return null;
    }
}

if (! function_exists('adminPrefix')) {
    /**
     * Admin prefix config shortcut
     * 
     * @return string
     */
    function adminPrefix(): string
    {
        return trim(config('core.adminPrefix', 'admin'), '/');
    }
}

if (! function_exists('ajaxPrefix')) {
    /**
     * Ajax prefix config shortcut
     * 
     * @return string
     */
    function ajaxPrefix(): string
    {
        return trim(config('core.ajaxPrefix', 'ajax'), '/');
    }
}

if (! function_exists('modules_path')) {
    function modules_path(): string
    {
        return base_path().'/Modules';
    }
}

if (! function_exists('class_machine_name')) {
    /**
     * Turns a class or object into a machine name
     * 
     * @param  object|string $class
     * @return string
     */
    function class_machine_name($class): string
    {
        $class = object_to_class($class);
        return Str::snake(class_basename($class));
    }
}

if (! function_exists('base_namespace')) {
    /**
     * Returns the base namespace of a class or object
     * 
     * @param  object|string $class
     * @return string
     */
    function base_namespace($class): string
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $name = class_basename($class);
        return substr($class, 0, strlen($class) - strlen($name) - 1);
    }
}

if (! function_exists('object_to_class')) {
    /**
     * @param  object|string $class
     * @return string
     */
    function object_to_class($class): string
    {
        if (is_object($class)) {
            return get_class($class);
        }
        return $class;
    }
}

if (! function_exists('class_to_object')) {
    /**
     * @param  object|string $class
     * @return object
     */
    function class_to_object($class): object
    {
        if (is_string($class)) {
            return new $class;
        }
        return $class;
    }
}

if (! function_exists('friendly_size')) {
    /**
     * Friendly size formatter. Takes a size in bytes.
     * 
     * @param int $size 
     * @param string $unit
     * 
     * @return string
     */
    function friendly_size(int $size, $unit = ''): string
    {
        $unit = strtoupper($unit);
        if ((!$unit && $size >= 1000*1000*1000) || $unit == "GB") {
            return number_format($size/(1000*1000*1000), 2)."Gb";
        }
        if ((!$unit && $size >= 1000*1000) || $unit == "MB") {
            return number_format($size/(1000*1000), 2)."Mb";
        }
        if ((!$unit && $size >= 1000) || $unit == "KB") {
            return number_format($size/(1000), 2)."Kb";
        }
        return number_format($size)." bytes";
    }
}

if (! function_exists('convertPhpToJsMomentFormat')) {
    /**
     * Converts php DateTime format to Javascript Moment format.
     * 
     * @param string $phpFormat
     * 
     * @return string
     */
    function convertPhpToJsMomentFormat(string $phpFormat): string
    {
        $replacements = [
            'A' => 'A',      // for the sake of escaping below
            'a' => 'a',      // for the sake of escaping below
            'B' => '',       // Swatch internet time (.beats), no equivalent
            'c' => 'YYYY-MM-DD[T]HH:mm:ssZ', // ISO 8601
            'D' => 'ddd',
            'd' => 'DD',
            'e' => 'zz',     // deprecated since version 1.6.0 of moment.js
            'F' => 'MMMM',
            'G' => 'H',
            'g' => 'h',
            'H' => 'HH',
            'h' => 'hh',
            'I' => '',       // Daylight Saving Time? => moment().isDST();
            'i' => 'mm',
            'j' => 'D',
            'L' => '',       // Leap year? => moment().isLeapYear();
            'l' => 'dddd',
            'M' => 'MMM',
            'm' => 'MM',
            'N' => 'E',
            'n' => 'M',
            'O' => 'ZZ',
            'o' => 'YYYY',
            'P' => 'Z',
            'r' => 'ddd, DD MMM YYYY HH:mm:ss ZZ', // RFC 2822
            'S' => 'o',
            's' => 'ss',
            'T' => 'z',      // deprecated since version 1.6.0 of moment.js
            't' => '',       // days in the month => moment().daysInMonth();
            'U' => 'X',
            'u' => 'SSSSSS', // microseconds
            'v' => 'SSS',    // milliseconds (from PHP 7.0.0)
            'W' => 'W',      // for the sake of escaping below
            'w' => 'e',
            'Y' => 'YYYY',
            'y' => 'YY',
            'Z' => '',       // time zone offset in minutes => moment().zone();
            'z' => 'DDD',
        ];

        // Converts escaped characters.
        foreach ($replacements as $from => $to) {
            $replacements['\\' . $from] = '[' . $from . ']';
        }

        return strtr($phpFormat, $replacements);
    }
}