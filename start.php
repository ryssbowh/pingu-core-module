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

function explodeCamelCase($str){
	return trim(implode(' ',preg_split('/(?=[A-Z])/', $str)));
}

function friendlyClassname($str){
	return explodeCamelCase(class_basename($str));
}

if (!function_exists('themes_path')) {

    function themes_path($filename = null)
    {
        return app()->make('core.themes')->themes_path($filename);
    }
}

if (!function_exists('theme_url')) {

    function theme_url($url)
    {
        return app()->make('core.themes')->url($url);
    }

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

function theme_config($value, $default = null)
{
    if(is_array($value)){
        return ThemeConfig::set($value);
    }
    return ThemeConfig::get($value, $default);
}

function replaceUriSlugs(string $uri, array $replacements){
    preg_match('/^.*(\{[a-zA-Z_]+\}).*$/', $uri, $matches);
    if(sizeof($matches)-1 != sizeof($replacements)){
        throw new UriReplacementsSize("Size of replacements (".sizeof($replacements).") doesn't match the size of replaceable entities (".(sizeof($matches)-1).") in $uri");
    }
    foreach($replacements as $i => $replacement){
        $uri = str_replace($matches[$i+1], $replacement, $uri);
    }
    return '/'.ltrim($uri, '/');
}