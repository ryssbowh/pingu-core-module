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

function friendly_field_name($name): string
{
    return Str::title(str_replace('_', ' ', $name));
}

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
    if (!$route = Route::getRoutes()->getByName($name)) {
        throw new RouteNameDoesNotExistsException("Route ".$name." doesn't exists");
    }
    return $route;
}

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
    
/**
 * upload_max_filesize in Kb
 */
function upload_max_filesize(): int
{
    return rtrim(ini_get('upload_max_filesize'), 'M')*1000;
}
    
/**
 * Temporary disk file path helper
 * 
 * @param  ?string $file
 * @return string
 */
function temp_path($file = null): string
{
    $path = config('filesystems.disks.tmp.root');
    if($file) {
        $path .= DIRECTORY_SEPARATOR.$file;
    }
    return $path;
}

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

/**
 * Admin prefix config shortcut
 * 
 * @return string
 */
function adminPrefix(): string
{
    return config('core.adminPrefix', '');
}

/**
 * Ajax prefix config shortcut
 * 
 * @return string
 */
function ajaxPrefix(): string
{
    return config('core.ajaxPrefix', '');
}

function modules_path(): string
{
    return base_path().'/Modules';
}

/**
 * Turns a class or object into a machine name
 * 
 * @param  object|string $class
 * @return string
 */
function class_machine_name($class): string
{
    $class = object_to_class($class);
    return strtolower(Str::studly(class_basename($class)));
}
    
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
    if ((!$unit && $size >= 1000*1000*1000) || $unit == "GB") {
        return number_format($size/(1000*1000*1000), 2)."GB";
    }
    if ((!$unit && $size >= 1000*1000) || $unit == "MB") {
        return number_format($size/(1000*1000), 2)."MB";
    }
    if ((!$unit && $size >= 1000) || $unit == "KB") {
        return number_format($size/(1000), 2)."KB";
    }
    return number_format($size)." bytes";
}