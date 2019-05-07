<?php

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

function classname($str){
	if(is_object($str)){
		$str = get_class($str);
	}
	return substr($str,strrpos($str,'\\')+1);
}

function explodeCamelCase($str){
	return trim(implode(' ',preg_split('/(?=[A-Z])/', $str)));
}

function friendlyClassname($str){
	return explodeCamelCase(classname($str));
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

function route_exists($uri)
{
    if(!is_string($uri)) return false;
    $uri = trim($uri, '/');
    $routes = \Route::getRoutes()->getRoutes();
    foreach($routes as $r){
        if($r->uri() == $uri and in_array('GET', $r->methods())){
            return true;
        }
    }
    return false;
}