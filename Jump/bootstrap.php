<?php
define('JUMP', __DIR__);

require_once JUMP . '/config/constants.php';
require_once 'autoload.php';


use Jump\Jump;
use Jump\helpers\Common;
use Jump\DI\DI;

try{
	// Dependency injection
    $di = DI::getInstance();
	
	// requiring services by a providers
    $services = require JUMP . '/config/services.php';
	
	foreach($services as $service){
		(new $service($di))->init();
	}
	
	require_once JUMP . '/helpers.php';
		
	// Инициализируем типы страниц по умолчанию
	//if(ENV == 'admin')
		include ROOT . 'admin/defaultPageTypes.php';
	
	// Plugins activating
	plugins(unserialize(Common::getOption('plugins_activated')));
	
	// User functions
	require_once THEME_DIR . 'functions.php';
	
	
	$di->set('models', []);
	
	$jump = new Jump($di);
	$jump->run();
}catch(Exception $e){
	Common::exceptionMessage($e);
}