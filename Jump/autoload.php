<?php

spl_autoload_register(function($class){
	$class = ROOT . $class . '.php';
	//var_dump($class);
	if(file_exists($class)){
		require_once $class;
	}
});