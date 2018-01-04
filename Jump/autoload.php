<?php

spl_autoload_register(function($class){
	$class = ROOT . str_replace('\\', '/', $class) . '.php';
	//var_dump($class);
	if(file_exists($class)){
		require_once $class;
	}
});