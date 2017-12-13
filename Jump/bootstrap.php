<?php
ini_set('xdebug.var_display_max_depth', 50);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
define('JUMP', __DIR__);

require_once JUMP . '/config/constants.php';
require_once 'autoload.php';

use Jump\Jump;
use Jump\helpers\Common;
use Jump\DI\DI;

try{
	// Dependency injection
    $di = new DI();
	
	// requiring services by a providers
    $services = require JUMP . '/config/services.php';
	
	foreach($services as $service){
		(new $service($di))->init();
	}
	
	require_once THEME_DIR . 'functions.php';
	
	
	$di->set('models', []);
	
	$jump = new Jump($di);
	$jump->run();
	
	
} catch(Exception $e){	
	Common::exceptionMessage($e);
}