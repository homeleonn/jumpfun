<?php

$aliases = [
	'DI' => Jump\DI\DI::class,
	'Router' => Jump\core\router\Router::class,
	'Options' => frontend\models\post\Options::class,
	'Cache' => Jump\core\cache\Cache::class,
	'Responce' => Jump\supports\facades\Responce::class,
	'DB' => Jump\supports\facades\DB::class,
];

spl_autoload_register(function($class){
	global $aliases;
	$className = $class;
	$isAlias = isset($aliases[$class]);
	if($isAlias) 
		$class = $aliases[$class];
	
	$classFile = str_replace('\\', '/', ROOT . $class) . '.php';
	
	if(!file_exists($classFile)){
		//return false;
		$backtrace = debug_backtrace();
		$stackLevel = isset($backtrace[2]['file']) ? 2 : 1;
		throw new Exception("Class '{$class}' is not found in {$backtrace[$stackLevel]['file']} on line {$backtrace[$stackLevel]['line']}. Exception ");
	}else{
		require_once $classFile;
	}
	
	if($isAlias) 
		class_alias($class, $className);
});