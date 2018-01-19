<?php

$aliases = [
	'DI' => \Jump\DI\DI::class,
	'Router' => \Jump\core\router\Router::class,
	'Options' => \frontend\models\post\Options::class,
	'Cache' => \Jump\core\cache\Cache::class,
];

spl_autoload_register(function($class){//var_dump($class);
	global $aliases;
	$className = $class;
	$isAlias = isset($aliases[$class]);
	if($isAlias) 
		$class = $aliases[$class];
	
	$classFile = str_replace('\\', '/', ROOT . $class) . '.php';
	
	if(!file_exists($classFile)){
		$backtrace = debug_backtrace();
		throw new Exception("Class '{$class}' is not found in {$backtrace[1]['file']} on line {$backtrace[1]['line']}. Exception ");
	}else{
		require_once $classFile;
	}
	
	if($isAlias) 
		class_alias($class, $className);
});