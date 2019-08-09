<?php

use Pingu\Core\Exceptions\RouteNameDoesNotExistsException;
use Pingu\Core\Exceptions\UriReplacementsSize;

/*
|--------------------------------------------------------------------------
| Register Namespaces and Routes
|--------------------------------------------------------------------------
|
| When your module starts, this file is executed automatically. By default
| it will only load the module's route file. However, you can expand on
| it to load anything else from the module, such as a class or view.
|
*/

/**
 * Explodes a string by camel case
 * @param  strign $str
 * @return string
 */
function explodeCamelCase($str){
	return trim(implode(' ',preg_split('/(?=[A-Z])/', $str)));
}

/**
 * Takes a fully namespaced class and returns its name, exploded by camel case
 * @param  string $str
 * @return string
 */
function friendlyClassname($str){
	return explodeCamelCase(class_basename($str));
}

/**
 * Returns the path of a given file for the current theme
 * @param  string $filename
 * @return string|null
 */
function themes_path($filename = null)
{
    return app()->make('core.themes')->themes_path($filename);
}

/**
 * Returns the url of a given resource for the current theme
 * @param  string $url
 * @return string|null
 */
function theme_url($url)
{
    return app()->make('core.themes')->url($url);
}

/**
 * Checks if a route exists as a GET action. Checks path and name
 * @param  string $uri
 * @return bool
 */
function route_exists($uri)
{
    if(!is_string($uri)) return false;
    $uri = trim($uri, '/');
    $routes = \Route::getRoutes()->getRoutes();
    foreach($routes as $r){
        if(($r->uri() == $uri or $uri == $r->getName()) and in_array('GET', $r->methods())){
            return true;
        }
    }
    return false;
}

/**
 * Search a route by its name
 * @param  string $name 
 * @return Route
 */
function route_by_name(string $name){
    if(!$route = Route::getRoutes()->getByName($name)){
        throw new RouteNameDoesNotExistsException("Route ".$name." doesn't exists");
    }
    return $route;
}

/**
 * Gets a theme config. If $value is not found, will return normal config
 * or the default if not found
 * @param  mixed $value
 * @param  mixed $default
 * @return mixed
 */
function theme_config($value, $default = null)
{
    if(is_array($value)){
        return ThemeConfig::set($value);
    }
    return ThemeConfig::get($value, $default);
}

/**
 * Takes an uri ex /admin/{menu}/{item} and an array of object for replacements.
 * Will replace the slugs by the route key name for each object in the array, by order of appearance.
 * @param  string $uri          [description]
 * @param  array  $replacements [description]
 * @return string
 */
function replaceUriSlugs(string $uri, array $replacements){
    preg_match('/^.*(\{[a-zA-Z0-9_\-]+\}).*$/', $uri, $matches);
    if(sizeof($matches)-1 != sizeof($replacements)){
        throw new UriReplacementsSize("Size of replacements (".sizeof($replacements).") doesn't match the size of replaceable entities (".(sizeof($matches)).") in $uri");
    }
    foreach($replacements as $i => $replacement){
        $uri = str_replace($matches[$i+1], $replacement->getRouteKey(), $uri);
    }
    return '/'.ltrim($uri, '/');
}
    
/**
 * upload_max_filesize in Kb
 */
function upload_max_filesize()
{
    return rtrim(ini_get('upload_max_filesize'),'M')*1000;
}

function temp_path($file = null){
    $path = config('filesystems.disks.tmp.root');
    if($file){
        $path .= DIRECTORY_SEPARATOR.$file;
    }
    return $path;
}

function namespace_from_file($file){
    $src = file_get_contents($file);
    if (preg_match('#^namespace\s+(.+?);$#sm', $src, $m)) {
        return $m[1];
    }
    return null;
}