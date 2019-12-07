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

function friendly_field_name($name)
{
    return Str::title(str_replace('_', ' ', $name));
}

/**
 * Explodes a string by camel case
 * 
 * @param  strign $str
 * @return string
 */
function explodeCamelCase($str)
{
    return trim(implode(' ', preg_split('/(?=[A-Z])/', $str)));
}

/**
 * Takes a fully namespaced class and returns its name, exploded by camel case
 * 
 * @param  string $str
 * @return string
 */
function friendlyClassname($class)
{
    $str = object_to_class($class);
    return explodeCamelCase(class_basename($str));
}

/**
 * Returns the path of a given file for the current theme
 * 
 * @param  string $filename
 * @return string|null
 */
function themes_path($filename = null)
{
    return app()->make('core.themes')->themes_path($filename);
}

/**
 * Returns the url of a given resource for the current theme
 * 
 * @param  string $url
 * @return string|null
 */
function theme_url($url)
{
    return app()->make('core.themes')->url($url);
}

/**
 * Checks if a route exists as a GET action. Checks path and name
 *
 * @param  string $uri
 * @return bool
 */
function route_exists($uri)
{
    if(!is_string($uri)) { return false;
    }
    $uri = trim($uri, '/');
    $routes = \Route::getRoutes()->getRoutes();
    foreach($routes as $r){
        if(($r->uri() == $uri or $uri == $r->getName()) and in_array('GET', $r->methods())) {
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
    if(!$route = Route::getRoutes()->getByName($name)) {
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
    foreach(app('router')->getRoutes()->getIterator() as $route){
        if($friendly = $route->getAction('friendly') and $route->getName()) {
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
function replaceUriSlugs(string $uri, array $replacements)
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
function upload_max_filesize()
{
    return rtrim(ini_get('upload_max_filesize'), 'M')*1000;
}
    
/**
 * Temporary disk file path helper
 * 
 * @param  ?string $file
 * @return string
 */
function temp_path($file = null)
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
function namespace_from_file($file)
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
function adminPrefix()
{
    return config('core.adminPrefix');
}

/**
 * Ajax prefix config shortcut
 * 
 * @return string
 */
function ajaxPrefix()
{
    return config('core.ajaxPrefix');
}

function modules_path()
{
    return base_path().'/Modules';
}

function class_machine_name($class)
{
    if (is_object($class)) {
        $class = get_class($class);
    }
    return strtolower(Str::studly(class_basename($class)));
}

function base_namespace($class)
{
    if (is_object($class)) {
        $class = get_class($class);
    }
    $name = class_basename($class);
    return substr($class, 0, strlen($class) - strlen($name) - 1);
}

function object_to_class($class)
{
    if (is_object($class)) {
        return get_class($class);
    }
    return $class;
}

function class_to_object($class)
{
    if (is_string($class)) {
        return new $class;
    }
    return $class;
}

function friendly_size($size, $unit = '')
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