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

if (!app()->routesAreCached()) {
    require __DIR__ . '/Http/routes.php';
}

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