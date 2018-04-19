<?php

namespace Jump\services;

use Jump\core\router\Router;

class RouterProvider extends AbstractProvider{
	private $serviceName = 'router';
	
	public function init(){
		$routes  = include ROOT . ENV . '/routes.php';
		$router  = new Router($routes);
		
		$this->di->set($this->serviceName, $router);
	}
}