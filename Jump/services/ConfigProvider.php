<?php

namespace Jump\services;

use Jump\core\config\Config;

class ConfigProvider extends AbstractProvider{
	private $serviceName = 'config';
	
	public function init(){
		$config  = new Config($this->di->get('db'));
		$config->setOption('frontend_deps', include ROOT . 'frontend/dependencies.php');
		
		$this->di->set($this->serviceName, $config);
	}
}